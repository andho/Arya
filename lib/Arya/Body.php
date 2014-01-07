<?php

namespace Arya;

interface Body {

    /**
     * Responsible for outputting entity body data to STDOUT
     */
    function __invoke();

    /**
     * Return an optional array of headers to be sent prior to entity body output
     */
    function getHeaders();

}
