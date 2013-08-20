<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Website {

    public function read($scopeCode)
    {
        /** Process websites config values from database */
        $deleteWebsites = array();
        $dbWebsiteConfig = array();
        foreach ($configData as $configValue) {
            if ($configValue['scope'] !== Mage_Core_Model_Config::SCOPE_WEBSITES) {
                continue;
            }
            $value = $configValue['value'];
            if (isset($webSiteList[$configValue['scope_id']])) {
                $configPath = $webSiteList[$configValue['scope_id']]['code'] . '/' . $configValue['path'];
                $dbWebsiteConfig[$configPath] = $value;
            } else {
                $deleteWebsites[$configValue['scope_id']] = $configValue['scope_id'];
            }
        }
        $dbWebsiteConfig = $this->_converter->convert($dbWebsiteConfig);

        /** Inherit default config values to all websites */
        foreach ($webSiteList as $configValue) {
            $code = $configValue['code'];
            $initialConfig['websites'][$code] = array_replace_recursive($initialConfig['default'], $initialConfig['websites'][$code]);
            if (isset($dbWebsiteConfig[$code])) {
                $initialConfig['websites'][$code] = array_replace_recursive($initialConfig['websites'][$code], $dbWebsiteConfig[$code]);
            }
        }

        /** Extend website config values to all associated stores */
        foreach ($webSiteList as $configValue) {
            $code = $configValue['code'];
            $extendData = $initialConfig['websites'][$code];
            if (isset($configValue[Mage_Core_Model_Config::SCOPE_STORES])) {
                foreach ($configValue[Mage_Core_Model_Config::SCOPE_STORES] as $storeCode) {
                    $initialConfig['stores'][$storeCode] = array_replace_recursive($extendData, $initialConfig['stores'][$storeCode]);
                }
            }
        }
    }
}
