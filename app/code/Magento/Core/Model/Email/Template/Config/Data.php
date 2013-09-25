<?php
/**
 * Email templates configuration data container. Provides email templates configuration data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_Config_Data extends Magento_Config_Data
{
    /**
     * @param Magento_Core_Model_Email_Template_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     */
    public function __construct(
        Magento_Core_Model_Email_Template_Config_Reader $reader,
        Magento_Config_CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'email_templates');
    }
}
