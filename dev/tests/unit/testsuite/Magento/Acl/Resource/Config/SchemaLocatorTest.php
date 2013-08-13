<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Resource_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var Magento_Acl_Resource_Config_SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        /** @todo Implement test logic here */
        
        $this->_model = new Magento_Acl_Resource_Config_SchemaLocator();
    }

    public function testGetSchema()
    {
        /** @todo Implement test logic here */
        
        $this->_model->getSchema();
    }

    public function testGetPerFileSchema()
    {
        /** @todo Implement test logic here */
        
        $this->_model->getPerFileSchema();
    }
}