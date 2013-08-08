<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Resource_Eav_AttributeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute');
    }

    public function testCRUD()
    {
        $this->_model->setAttributeCode('test')
            ->setEntityTypeId(Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId())
            ->setFrontendLabel('test');
        $crud = new Magento_Test_Entity($this->_model, array('frontend_label' => uniqid()));
        $crud->testCrud();
    }
}
