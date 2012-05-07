<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_Method_CashondeliveryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Payment_Model_Method_Cashondelivery
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Mage_Payment_Model_Method_Cashondelivery;
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Mage_Payment_Block_Info_Instructions', $this->_object->getInfoBlockType());
    }
}
