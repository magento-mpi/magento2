<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetAllowedCurrencies()
    {
        $currencyPath = 'cur/ren/cy/path';
        $expectedResult = array('EUR', 'USD');

        $configMock = $this->getMock('Magento\App\Config\ReinitableConfigInterface', array(), array(), '', false);
        $configMock->expects($this->once())
            ->method('getValue')
            ->with($currencyPath, 'store', null)
            ->will($this->returnValue('EUR,USD'));

        /** @var \Magento\Store\Model\Store $model */
        $model = $this->objectManager->getObject('Magento\Store\Model\Store', array(
            'config' => $configMock,
            'currencyInstalled' => $currencyPath,
        ));

        $this->assertEquals($expectedResult, $model->getAllowedCurrencies());
    }
}