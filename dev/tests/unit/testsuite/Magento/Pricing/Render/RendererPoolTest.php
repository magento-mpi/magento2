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
 * Test class for \Magento\Pricing\Render\RendererPool
 */
class RendererPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Render\RendererPool | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /**
     * @var \Magento\Core\Model\Layout | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Core\Model\Layout | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->layoutMock = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder('\Magento\View\Element\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layoutMock));
    }

    /**
     * Test createPriceRender() if not found render class name
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class name for price code "price_test" not registered
     */
    public function testCreatePriceRenderNoClassName()
    {
        $methodData = [];
        $priceCode = 'price_test';
        $data = [];
        $type = 'simple';
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));

        $testedClass = $this->createTestedEntity($data);
        $result = $testedClass->createPriceRender($priceCode, $productMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test createPriceRender() if not found price model
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Price model for price code "price_test" not registered
     */
    public function testCreatePriceRenderNoPriceModel()
    {
        $methodData = [];
        $priceCode = 'price_test';
        $type = 'simple';
        $className = 'Test';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'render_class' => $className
                    ]
                ]
            ]
        ];
        $priceModel = null;

        $priceInfoMock = $this->getMockBuilder('Magento\Pricing\PriceInfo\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($priceModel));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfoMock));

        $testedClass = $this->createTestedEntity($data);
        $result = $testedClass->createPriceRender($priceCode, $productMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test createPriceRender() if not found price model
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Block "Magento\View\Element\Template\Context" must implement \Magento\Pricing\Render\PriceBoxRenderInterface
     */
    public function testCreatePriceRenderBlockNotPriceBox()
    {
        $methodData = [];
        $priceCode = 'price_test';
        $type = 'simple';
        $className = 'Magento\View\Element\Template\Context';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'render_class' => $className
                    ]
                ]
            ]
        ];

        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock = $this->getMockBuilder('Magento\Pricing\PriceInfo\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($priceModelMock));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfoMock));

        $contextMock = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $block = new \Magento\View\Element\Template($contextMock);

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'price' => $priceModelMock,
            'saleableItem' => $productMock
        ];
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($className), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($block));

        $result = $testedClass->createPriceRender($priceCode, $productMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test createPriceRender()
     */
    public function testCreatePriceRender()
    {
        $methodData = [];
        $priceCode = 'price_test';
        $type = 'simple';
        $className = 'Magento\View\Element\Template\Context';
        $template = 'template.phtml';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'render_class' => $className,
                        'render_template' => $template
                    ]
                ]
            ]
        ];

        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock = $this->getMockBuilder('Magento\Pricing\PriceInfo\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($priceModelMock));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfoMock));

        $renderBlock = $this->getMockBuilder('Magento\Pricing\Render\PriceBox')
            ->disableOriginalConstructor()
            ->getMock();
        $renderBlock->expects($this->once())
            ->method('setTemplate')
            ->with($this->equalTo($template));

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'price' => $priceModelMock,
            'saleableItem' => $productMock
        ];
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($className), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($renderBlock));

        $result = $testedClass->createPriceRender($priceCode, $productMock, $methodData);
        $this->assertInstanceOf('Magento\Pricing\Render\PriceBoxRenderInterface', $result);
    }

    /**
     * Test createAmountRender() if amount render class not found
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no amount render class for price code "base_price_test"
     */
    public function testCreateAmountRenderNoAmountClass()
    {
        $data = [];
        $type = 'simple';
        $methodData = [];
        $priceCode = 'base_price_test';

        $amountMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceModelMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceCode));

        $testedClass = $this->createTestedEntity($data);
        $result = $testedClass->createAmountRender($amountMock, $productMock, $priceModelMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test createAmountRender() if amount render block not implement Amount interface
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Block "Magento\View\Element\Template\Context" must implement \Magento\Pricing\Render\AmountRenderInterface
     */
    public function testCreateAmountRenderNotAmountInterface()
    {
        $type = 'simple';
        $methodData = [];
        $priceCode = 'base_price_test';
        $amountRenderClass = 'Magento\View\Element\Template\Context';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'amount_render_class' => $amountRenderClass
                    ]
                ]
            ]
        ];

        $amountMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceModelMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceCode));

        $contextMock = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $block = new \Magento\View\Element\Template($contextMock);

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'amount' => $amountMock,
            'saleableItem' => $productMock,
            'price' => $priceModelMock
        ];

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($amountRenderClass), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($block));

        $result = $testedClass->createAmountRender($amountMock, $productMock, $priceModelMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test createAmountRender()
     */
    public function testCreateAmountRender()
    {
        $type = 'simple';
        $methodData = [];
        $priceCode = 'base_price_test';
        $template = 'template.phtml';
        $amountRenderClass = 'Magento\Pricing\Render\Amount';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'amount_render_class' => $amountRenderClass,
                        'amount_render_template' => $template
                    ]
                ]
            ]
        ];

        $amountMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceModelMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceCode));

        $blockMock = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->getMock();

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'amount' => $amountMock,
            'saleableItem' => $productMock,
            'price' => $priceModelMock
        ];

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($amountRenderClass), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($blockMock));

        $blockMock->expects($this->once())
            ->method('setTemplate')
            ->with($this->equalTo($template));

        $result = $testedClass->createAmountRender($amountMock, $productMock, $priceModelMock, $methodData);
        $this->assertInstanceOf('Magento\Pricing\Render\AmountRenderInterface', $result);
    }

    /**
     * Test getAdjustmentRenders()
     */
    public function testGetAdjustmentRenders()
    {
        $saleableTypeId = 'test_type';
        $priceType = 'test_price_type';
        $code = 'tax';
        $class = 'Magento\View\Element\Template';
        $template = 'template.phtml';
        $adjustments = [
            'adjustment_render_class' => $class,
            'adjustment_render_template' => $template
        ];
        $data = [$saleableTypeId => [
            'adjustments' => [
                $priceType => [
                    $code => $adjustments
                ]
            ]
        ]];
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($saleableTypeId));

        $price = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $price->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceType));

        $blockMock = $this->getMockBuilder('Magento\View\Element\Template')
            ->disableOriginalConstructor()
            ->getMock();
        $blockMock->expects($this->once())
            ->method('setTemplate')
            ->with($this->equalTo($template));

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($class))
            ->will($this->returnValue($blockMock));

        $testedClass = $this->createTestedEntity($data);
        $result = $testedClass->getAdjustmentRenders($saleable, $price);
        $this->assertArrayHasKey($code, $result);
        $this->assertInstanceOf('Magento\View\Element\Template', $result[$code]);
    }

    /**
     * Test getAmountRenderBlockTemplate() through createAmountRender() in case when template not exists
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage For type "simple" amount render block not configured
     */
    public function testGetAmountRenderBlockTemplateNoTemplate()
    {
        $type = 'simple';
        $methodData = [];
        $priceCode = 'base_price_test';
        $template = false;
        $amountRenderClass = 'Magento\Pricing\Render\Amount';
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'amount_render_class' => $amountRenderClass,
                        'amount_render_template' => $template
                    ]
                ]
            ]
        ];

        $amountMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceModelMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue($priceCode));

        $blockMock = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->getMock();

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'amount' => $amountMock,
            'saleableItem' => $productMock,
            'price' => $priceModelMock
        ];

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($amountRenderClass), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($blockMock));

        $result = $testedClass->createAmountRender($amountMock, $productMock, $priceModelMock, $methodData);
        $this->assertNull($result);
    }

    /**
     * Test getRenderBlockTemplate() through createPriceRender() in case when template not exists
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Price code "price_test" render block not configured
     */
    public function testGetRenderBlockTemplate()
    {
        $methodData = [];
        $priceCode = 'price_test';
        $type = 'simple';
        $className = 'Magento\View\Element\Template\Context';
        $template = false;
        $data = [
            $type => [
                'prices' => [
                    $priceCode => [
                        'render_class' => $className,
                        'render_template' => $template
                    ]
                ]
            ]
        ];

        $priceModelMock = $this->getMockBuilder('Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock = $this->getMockBuilder('Magento\Pricing\PriceInfo\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($priceModelMock));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($type));
        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfoMock));

        $renderBlock = $this->getMockBuilder('Magento\Pricing\Render\PriceBox')
            ->disableOriginalConstructor()
            ->getMock();

        $testedClass = $this->createTestedEntity($data);

        $arguments = [
            'data' => $methodData,
            'rendererPool' => $testedClass,
            'price' => $priceModelMock,
            'saleableItem' => $productMock
        ];
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($className), $this->equalTo(''), $this->equalTo($arguments))
            ->will($this->returnValue($renderBlock));

        $result = $testedClass->createPriceRender($priceCode, $productMock, $methodData);
        $this->assertInstanceOf('Magento\Pricing\Render\PriceBoxRenderInterface', $result);
    }

    /**
     * Create tested object with specified parameters
     *
     * @param array $data
     * @return RendererPool
     */
    protected function createTestedEntity(array $data = [])
    {
        return $this->object = new \Magento\Pricing\Render\RendererPool($this->contextMock, $data);
    }
}
