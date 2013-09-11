<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_Config_Converter_DomTest extends Magento_Acl_Resource_Config_Converter_DomTest
{
    protected function setUp()
    {
        $this->_converter = new \Magento\Webapi\Model\Acl\Resource\Config\Converter\Dom();
    }

    /**
     * @return array
     */
    public function convertWithValidDomDataProvider()
    {
        return array(
            array(
                include __DIR__ . DIRECTORY_SEPARATOR . '_files'
                    . DIRECTORY_SEPARATOR . 'converted_valid_webapi_acl.php',
                file_get_contents(
                    __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'valid_webapi_acl.xml'
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function convertWithInvalidDomDataProvider()
    {
        return array_merge(
            parent::convertWithInvalidDomDataProvider(),
            array(
                array(
                    'mapping without "id" attribute' => '<?xml version="1.0"?><config><mapping>'
                        . '<resource parent="Custom_Module::parent_id" /></mapping></config>'
                ),
                array(
                    'mapping without "parent" attribute' => '<?xml version="1.0"?><config><mapping>'
                        . '<resource id="Custom_Module::id" /></mapping></config>'
                ),
            )
        );
    }
}
