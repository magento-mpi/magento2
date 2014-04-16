<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Render;

/**
 * Test class for \Magento\Pricing\Render\PriceBox
 */
class PriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var PriceBox
     */
    protected $model;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Pricing\Render\RendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererPool;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleable;

    /**
     * @var \Magento\Pricing\Price\PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $price;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rendererPool = $this->getMockBuilder('Magento\Pricing\Render\RendererPool')
            ->disableOriginalConstructor()
            ->setMethods(['createAmountRender'])
            ->getMock();

        $layout = $this->getMock('Magento\Framework\View\LayoutInterface');
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $storeConfig = $this->getMockBuilder('Magento\Store\Model\Store\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));
        $this->context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));
        $this->context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($storeConfig));
        $this->context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfigMock));

        $this->saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');

        $this->price = $this->getMock('Magento\Pricing\Price\PriceInterface');

        $this->model = $this->objectManager->getObject('Magento\Pricing\Render\PriceBox', array(
            'context' => $this->context,
            'saleableItem' => $this->saleable,
            'price' => $this->price,
            'rendererPool' => $this->rendererPool
        ));
    }

    /**
     * @param array $data
     * @param string $priceType
     * @param array $cssClasses
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($data, $priceType, $cssClasses)
    {
        $this->price->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceType));

        $priceBox = $this->objectManager->getObject('Magento\Pricing\Render\PriceBox', array(
            'context' => $this->context,
            'saleableItem' => $this->saleable,
            'price' => $this->price,
            'rendererPool' => $this->rendererPool,
            'data' => $data
        ));
        $priceBox->toHtml();
        $this->assertEquals($cssClasses, $priceBox->getData('css_classes'));
    }

    public function toHtmlDataProvider()
    {
        return array(
            array(
                'data' => [],
                'price_type' => 'test_price',
                'css_classes' => 'price-test_price'
            ),
            array(
                'data' => ['css_classes' => 'some_css_class'],
                'price_type' => 'test_price',
                'css_classes' => 'some_css_class price-test_price'
        ));
    }

    public function testGetSaleableItem()
    {
        $this->assertEquals($this->saleable, $this->model->getSaleableItem());
    }

    public function testGetPrice()
    {
        $this->assertEquals($this->price, $this->model->getPrice());
    }

    public function testGetPriceType()
    {
        $priceCode = 'test_price';
        $quantity = 1.;

        $price = $this->getMock('Magento\Pricing\Price\PriceInterface');

        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($priceCode, $quantity)
            ->will($this->returnValue($price));

        $this->saleable->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        $this->assertEquals($price, $this->model->getPriceType($priceCode, $quantity));
    }

    public function testRenderAmount()
    {
        $amount = $this->getMock('Magento\Pricing\Amount\AmountInterface');
        $arguments = [];
        $resultHtml = 'result_html';

        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['toHtml'])
            ->getMock();
        $amountRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($resultHtml));

        $this->rendererPool->expects($this->once())
            ->method('createAmountRender')
            ->with($amount, $this->saleable, $this->price, $arguments)
            ->will($this->returnValue($amountRender));

        $this->assertEquals($resultHtml, $this->model->renderAmount($amount, $arguments));
    }
}
