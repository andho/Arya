<?php

use Arya\Application,
    Arya\Request,
    Arya\Response,
    Arya\Sessions\Session;

function helloFunction(Request $request) {
    return '<html><body><h1>Hello, World.</h1></body></html>';
}

$lambda = function(Request $request) {
    return '<html><body><h1>Hello from $lambda!</h1></body></html>';
};

class StaticExampleClass {
    public static function staticMethod(Request $request) {
        $msg = 'Hello from StaticExampleClass::staticMethod()';
        $body = sprintf("<html><body><h1>%s</h1</body></html>", $msg);
        return new Response($body, $status = 200);
    }
}

class CtorDependencies {
    private $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }
    public function myInstanceMethod() {
        $headline = "Hello from CtorDependencies::someMethod()";
        $subheading = "Composed Request Vars";

        return sprintf(
            "<html><body><h1>%s</h1><hr/><h4>%s</h4><pre>%s</pre></body></html>",
            $headline,
            $subheading,
            print_r($this->request->getAllVars(), TRUE)
        );
    }
}

function complexResponse(Request $request) {
    $response = (new Response)
        ->setHeader('X-My-Header', 'some-value')
        ->addHeader('X-My-Header', 'HTTP header fields can have multiple values!')
        ->setAllHeaders(array(
            'Header1' => 'header 1 value',
            'Header2' => 'header 2 value',
        ))
        ->setStatus(200)
        ->setReason('OK')
        ->setBody('<html><body><h1>Complex Response</h1></body></html>')
    ;

    return $response;
}

function argFunction(Request $request, $arg1) {
    $routeArgs = print_r($request['ROUTE_ARGS'], TRUE);
    $funcArg = print_r($arg1, TRUE);
    $body = sprintf('$request[\'ROUTE_ARGS\']: %s | $funcArgs: %s', $routeArgs, $funcArg);

    return (new Response)
        ->setHeader('Content-Type', 'text/plain;charset=utf-8')
        ->setBody($body)
    ;
}

function numericArgsFunction(Request $request) {
    $body = "<html><body><h1>numericArgsFunction</h1><pre>%s</pre></body></html>";
    $routeArgs = print_r($request['ROUTE_ARGS'], TRUE);

    return sprintf($body, $routeArgs);
}

function output() {
    echo "Output generated by your application generates an error";

    return "You won't see this because the output above will cause a 500 error";
}

function session(Session $session) {
    if (empty($session['test'])) {
        $session['test'] = 0;
    }

    $session['test'] += 1;

    return sprintf("<html><body><h1>\$session['test']: %d</h1></body></html>", $session['test']);
}

function php_info() {
    $info = ob_start();
    phpinfo();
    return ob_get_clean();
}

function fatalFunction() {
    $obj->nonexistent();
}

function exceptionFunction() {
    throw new \Exception('test');
}

function testComplexResponseFunctionTarget(Request $request) {
    $response = new Response;

    return $response->setStatus(234)
        ->setReasonPhrase('Custom Reason')
        ->setHeader('X-My-Header', '1')
        ->addHeader('X-My-Header', '2')
        ->setBody('zanzibar!')
    ;
}

function printVars($request) {
    return "<pre>" . print_r($request->all(), TRUE)  . "</pre>";
}

$app = new Application;
$app->route('GET', '/', 'helloFunction')
    ->route('GET', '/lambda', $lambda)
    ->route('GET', '/static-method', 'StaticExampleClass::staticMethod')
    ->route('GET', '/ctor-deps', 'CtorDependencies::myInstanceMethod')
    ->route('GET', '/complex-response', 'complexResponse')
    ->route('GET', '/phpinfo', 'php_info')
    ->route('GET', '/args/{arg1}', 'argFunction')
    ->route('GET', '/args/{arg1:\d+}/{arg2:\d+}', 'numericArgsFunction')
    ->route('GET', '/output', 'output')
    ->route('GET', '/session', 'session')
    ->route('GET', '/complex', 'testComplexResponseFunctionTarget')
    ->route('GET', '/print-vars', 'printVars')
    ->route('GET', '/fatal', 'fatalFunction')
    ->route('GET', '/exception', 'exceptionFunction')
    ->run()
;
