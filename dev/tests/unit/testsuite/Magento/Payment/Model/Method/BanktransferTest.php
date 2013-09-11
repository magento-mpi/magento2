<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Method_BanktransferTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Method\Banktransfer
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new \Magento\Payment\Model\Method\Banktransfer;
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('\Magento\Payment\Block\Info\Instructions', $this->_object->getInfoBlockType());
    }
}
