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

namespace Magento\Payment\Model\Method;

class BanktransferTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Method\Banktransfer
     */
    protected $_object;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $this->_object = new \Magento\Payment\Model\Method\Banktransfer($eventManager, $paymentDataMock);
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento\Payment\Block\Info\Instructions', $this->_object->getInfoBlockType());
    }
}
