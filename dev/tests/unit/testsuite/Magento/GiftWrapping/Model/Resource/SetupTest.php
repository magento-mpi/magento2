<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Resource;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftWrapping\Model\Resource\Setup
     */
    protected $giftRegistrySetup;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $setupFactoryMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->typeConfigMock = $this->getMock('\Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->typeFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\TypeFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->setupFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\SetupFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->giftRegistrySetup = $helper->getObject(
            'Magento\GiftWrapping\Model\Resource\Setup',
            array(
                'productTypeConfig' => $this->typeConfigMock,
                'productTypeFactory' => $this->typeFactoryMock,
                'catalogSetupFactory' => $this->setupFactoryMock,
            )
        );
    }

    public function testGetRealProductTypes()
    {
        $expected = array('simple', 'simple2');
        $this->typeConfigMock->expects(
            $this->once()
        )->method(
            'filter'
        )->with(
            'is_real_product'
        )->will(
            $this->returnValue($expected)
        );
        $this->assertEquals($expected, $this->giftRegistrySetup->getRealProductTypes());
    }

    public function testGetProductTypes()
    {
        $typeMock = $this->getMock('\Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->typeFactoryMock->expects($this->once())->method('create')->will($this->returnValue($typeMock));
        $this->assertEquals($typeMock, $this->giftRegistrySetup->getProductType());
    }

    public function testGetCatalogSetup()
    {
        $setupMock = $this->getMock('\Magento\Catalog\Model\Resource\Setup', [], [], '', false);
        $this->setupFactoryMock->expects($this->once())
            ->method('create')
            ->with(['resourceName' => 'catalog_setup'])
            ->will($this->returnValue($setupMock));
        $this->assertEquals($setupMock, $this->giftRegistrySetup->getCatalogSetup());
    }
}
