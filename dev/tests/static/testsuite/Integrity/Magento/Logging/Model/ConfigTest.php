<?php
/**
 * Find "logging.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Magento_Logging_Model_ConfigTest extends Integrity_ConfigAbstract
{
    /**
     * Returns the name of the xml files to validate
     *
     * @return string
     */
    protected function _getXMLName() {
        return 'logging.xml';
    }

    /**
     * Returns the name of the XSD file to be used to validate the XSD
     *
     * @return string
     */
    protected function _getXSDFile() {
        return '/app/code/Magento/Logging/etc/logging.xsd';
    }
}
