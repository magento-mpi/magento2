<?php
/**
 * \Magento\Wishlist\Block\Item\Configure
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Block\Item;

class ConfigureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Wishlist\Block\Item\Configure
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockCoreData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockWishlistData;

    public function setUp()
    {
        $this->_mockWishlistData = $this->getMockBuilder('Magento\Wishlist\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockCoreData = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockContext = $this->getMockBuilder('Magento\Core\Block\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRegistry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = new \Magento\Wishlist\Block\Item\Configure($this->_mockWishlistData,
            $this->_mockCoreData,
            $this->_mockContext,
            $this->_mockRegistry);
    }

    public function testGetProduct()
    {
        $product = 'some test product';
        $this->_mockRegistry->expects($this->once())
            ->method('registry')
            ->with($this->equalTo('product'))
            ->will($this->returnValue($product));

        $this->assertEquals($product, $this->_model->getProduct());
    }
}
