<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\PricePermissions;

class NewObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewObject
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricePerDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->requestMock = $this->getMock('\Magento\Framework\App\RequestInterface');
        $this->pricePerDataMock = $this->getMock('\Magento\PricePermissions\Helper\Data', array(), array(), '', false);
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array(
                '__wakeup',
                'setIsRecurring',
                'isObjectNew',
                'getTypeId',
                'getPriceType',
                'setPrice',
                'setGiftcardAmounts',
                'unsRecurringPayment',
                'setMsrpEnabled',
                'setMsrpDisplayActualPriceType'
            ),
            array(),
            '',
            false
        );

        $this->pricePerDataMock->expects(
            $this->once()
        )->method(
            'getDefaultProductPriceString'
        )->will(
            $this->returnValue('0.00')
        );

        $this->model = new NewObject($this->storeManagerMock, $this->requestMock, $this->pricePerDataMock);
    }

    public function testHandleWithNotNewProduct()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));
        $this->productMock->expects($this->never())->method('setIsRecurring');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithDynamicProductPrice()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPriceType'
        )->will(
            $this->returnValue(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC)
        );

        $this->productMock->expects($this->never())->method('setPrice');

        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpEnabled'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }

    public function testHandleWithGiftCardProductType()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD)
        );

        $this->productMock->expects($this->once())->method('setPrice')->with('0.0');

        $this->requestMock->expects($this->once())->method('getParam')->with('store')->will($this->returnValue(10));
        $storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue(5));
        $this->storeManagerMock->expects(
            $this->once()
        )->method(
            'getStore'
        )->with(
            10
        )->will(
            $this->returnValue($storeMock)
        );

        $this->productMock->expects(
            $this->once()
        )->method(
            'setGiftcardAmounts'
        )->with(
            array(array('website_id' => 5, 'price' => 0.0, 'delete' => ''))
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpEnabled'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }

    public function testHandleWithNonGiftCardProductType()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('some product type'));

        $this->productMock->expects($this->once())->method('setPrice')->with('0.0');

        $this->productMock->expects($this->never())->method('setGiftcardAmounts');

        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpEnabled'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }
}
