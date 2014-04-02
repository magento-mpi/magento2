<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $_helperData;

    protected function setUp()
    {
        $this->_product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\ConfigInterface', [], [], '', false);
        $coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(true));
        $weeeTax = $this->getMock('Magento\Weee\Model\Tax', [], [], '', false);
        $weeeTax->expects($this->any())
            ->method('getWeeeAmount')
            ->will($this->returnValue('11.26'));
        $arguments = array(
            'coreStoreConfig' => $coreStoreConfig,
            'weeeTax' => $weeeTax
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helperData = $helper->getObject('Magento\Weee\Helper\Data', $arguments);
    }

    public function testGetAmount()
    {
        $this->assertEquals('11.26', $this->_helperData->getAmount($this->_product));
    }
}
