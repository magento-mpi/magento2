<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Payment\Helper\Data
 */
namespace Magento\Payment\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInfoBlock()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Payment\Helper\Data');
        $paymentInfo = \Mage::getModel('Magento\Payment\Model\Info');
        $paymentInfo->setMethod('checkmo');
        $result = $helper->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Magento\Payment\Block\Info\Checkmo', $result);
    }
}
