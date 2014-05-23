<?php

namespace Magento\Composer\Extractor;

class FrontendThemeExtractor extends  AdminThemeExtractor {

    public function __construct($rootDir, $logger){
        parent::__construct($rootDir, $logger);
        $this->_path = $rootDir . '/app/design/frontend/Magento/';
    }

    public function getType(){
        return "magento2-theme-frontend";
    }

}