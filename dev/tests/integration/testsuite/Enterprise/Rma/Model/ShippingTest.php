<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Model_ShippingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Rma_Model_Shipping
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_Rma_Model_Shipping();
    }

    public function testIsCustom()
    {
        $this->_model->setCarrierCode('ups');
        $this->assertFalse($this->_model->isCustom());
        $this->_model->setCarrierCode('custom');
        $this->assertTrue($this->_model->isCustom());
    }
}
