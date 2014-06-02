<?php

namespace Magento\Composer;

interface Extractor {

    public function extract($collection = array(), &$count = 0);

    public function create(\Magento\Composer\Model\ArrayAndObjectAccess $definition);

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition);

    public function getType();

    public function getPath();

    public function createComponent($name);

    public function getParser($filename);


}