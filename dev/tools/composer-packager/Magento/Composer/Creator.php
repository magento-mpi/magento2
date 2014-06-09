<?php

namespace Magento\Composer;

interface Creator {

    public function __construct($components, \Zend_Log $logger);

    public function create();
}