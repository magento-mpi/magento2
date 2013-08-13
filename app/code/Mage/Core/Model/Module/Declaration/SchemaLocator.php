<?php
/**
 * Modules configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Module_Declaration_SchemaLocator implements Magento_Config_SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../../../etc/module.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return null
     */
    public function getPerFileSchema()
    {
        return null;
    }
}
