<?php
/**
 * Represents email templates data for a given scope.
 * Provides an abstraction from where the actual data is coming from: config files in the file system, or cache.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_Config_Data extends Magento_Config_Data_SingleScope
{
    /**
     * @param Magento_Core_Model_Email_Template_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     */
    public function __construct(
        Magento_Core_Model_Email_Template_Config_Reader $reader,
        Magento_Config_CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'email_templates', Magento_Core_Model_App_Area::AREA_GLOBAL);
    }
}
