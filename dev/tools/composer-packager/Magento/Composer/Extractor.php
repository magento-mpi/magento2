<?php

namespace Magento\Composer;

interface Extractor {

    public function extract($collection = array(), &$count = 0);

    public function createAndAdd(\Magento\Composer\Model\ArrayAndObjectAccess $definition);

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition);

    public function getPath();

    public function getType();

    public function createComponent($name);

    public function getParser($filename);


}