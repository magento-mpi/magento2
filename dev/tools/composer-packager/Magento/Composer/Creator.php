<?php

namespace Magento\Composer;

interface Creator {

    public function __construct($components, \Magento\Composer\Log\Log $logger);

    public function create();
}