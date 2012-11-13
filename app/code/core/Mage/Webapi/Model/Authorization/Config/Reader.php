<?php
/**
 * Api Acl Config Reader model
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_Config_Reader extends Magento_Acl_Config_Reader
{
    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'acl.xsd';
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><acl></acl><mapping></mapping></config>';
    }
}
