<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Model_TagTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tag_Model_Tag
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento_Tag_Model_Tag');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Magento_Core_Model_AppInterface::ADMIN_STORE_ID));
        $this->_model->setName('test');
        $crud = new Magento_Test_Entity($this->_model, array('name' => uniqid()));
        $crud->testCrud();
    }
}
