<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Model_Resource_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_User_Model_Resource_User */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
    }

    public function testCountAll()
    {
        $this->assertSame(1, $this->_model->countAll());
    }

    public function testGetValidationRulesBeforeSave()
    {
        $rules = $this->_model->getValidationRulesBeforeSave();
        $this->assertInstanceOf('Zend_Validate_Interface', $rules);
    }
}
