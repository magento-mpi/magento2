<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Source;

class CountryofmanufactureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $storeMock;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $cacheConfig;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->cacheConfig = $this->getMock('\Magento\Framework\App\Cache\Type\Config', array(), array(), '', false);
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Test for getAllOptions method
     *
     * @param $cachedDataSrl
     * @param $cachedDataUnsrl
     *
     * @dataProvider testGetAllOptionsDataProvider
     */
    public function testGetAllOptions($cachedDataSrl, $cachedDataUnsrl)
    {
        $this->storeMock->expects($this->once())->method('getCode')->will($this->returnValue('store_code'));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->cacheConfig->expects($this->once())
            ->method('load')
            ->with($this->equalTo('COUNTRYOFMANUFACTURE_SELECT_STORE_store_code'))
            ->will($this->returnValue($cachedDataSrl));

        $countryOfManufacture = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture',
            [
                'storeManager' => $this->storeManagerMock,
                'configCacheType' => $this->cacheConfig,
            ]
        );
        $this->assertEquals($cachedDataUnsrl, $countryOfManufacture->getAllOptions());
    }

    /**
     * Data provider for testGetAllOptions
     *
     * @return array
     */
    public function testGetAllOptionsDataProvider()
    {
        return
            [
                ['cachedDataSrl' => 'a:1:{s:3:"key";s:4:"data";}', 'cachedDataUnsrl' => ['key' => 'data']]
            ];
    }
}
