<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Catalog\Product\Type;

class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_customOptions;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_productResource;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Option
     */
    protected $_optionResource;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * @var \Magento\Sales\Model\Quote\Item\Option
     */
    protected $_quoteItemOption;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_store = $this->getMock(
            'Magento\Store\Model\Store',
            array('getCurrentCurrencyRate', '__sleep', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_storeManagerMock = $this->getMockBuilder(
            'Magento\Framework\StoreManagerInterface'
        )->disableOriginalConstructor()->setMethods(
            array('getStore')
        )->getMockForAbstractClass();
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));
        $this->_mockModel(array('_isStrictProcessMode'));
    }

    /**
     * Create model Mock
     *
     * @param $mockedMethods
     */
    protected function _mockModel($mockedMethods)
    {
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);
        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();
        $catalogData = $this->getMockBuilder('Magento\Catalog\Helper\Data')->disableOriginalConstructor()->getMock();
        $filesystem =
            $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()->getMock();
        $storage = $this->getMockBuilder(
            'Magento\Core\Helper\File\Storage\Database'
        )->disableOriginalConstructor()->getMock();
        $locale = $this->getMock('Magento\Framework\Locale\Format', array('getNumber'), array(), '', false);
        $locale->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $coreRegistry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $productOption = $this->getMock('Magento\Catalog\Model\Product\Option', array(), array(), '', false);
        $eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);
        $productTypeMock = $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false);
        $this->_model = $this->getMock(
            'Magento\GiftCard\Model\Catalog\Product\Type\Giftcard',
            $mockedMethods,
            array(
                $productFactory,
                $productOption,
                $eavConfigMock,
                $productTypeMock,
                $eventManager,
                $coreData,
                $storage,
                $filesystem,
                $coreRegistry,
                $logger,
                $catalogData,
                $this->_storeManagerMock,
                $locale,
                $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface')
            )
        );
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _preConditions()
    {
        $this->_store->expects($this->any())->method('getCurrentCurrencyRate')->will($this->returnValue(1));
        $this->_productResource = $this->getMock(
            'Magento\Catalog\Model\Resource\Product',
            array(),
            array(),
            '',
            false
        );
        $this->_optionResource = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Option',
            array(),
            array(),
            '',
            false
        );

        $productCollection = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Collection',
            array(),
            array(),
            '',
            false
        );

        $itemFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Configuration\Item\OptionFactory',
            array(),
            array(),
            '',
            false
        );
        $stockItemFactoryMock = $this->getMock(
            'Magento\CatalogInventory\Model\Stock\ItemFactory',
            array('create'),
            array(),
            '',
            false
        );
        $productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create'),
            array(),
            '',
            false
        );
        $categoryFactoryMock = $this->getMock(
            'Magento\Catalog\Model\CategoryFactory',
            array('create'),
            array(),
            '',
            false
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\Catalog\Model\Product',
            array(
                'itemOptionFactory' => $itemFactoryMock,
                'stockItemFactory' => $stockItemFactoryMock,
                'productFactory' => $productFactoryMock,
                'categoryFactory' => $categoryFactoryMock,
                'resource' => $this->_productResource,
                'resourceCollection' => $productCollection,
                'collectionFactory' => $this->getMock(
                        'Magento\Framework\Data\CollectionFactory',
                        array(),
                        array(),
                        '',
                        false
                    )
            )
        );
        $this->_product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getGiftcardAmounts', 'getAllowOpenAmount', 'getOpenAmountMax', 'getOpenAmountMin', '__wakeup'),
            $arguments,
            '',
            false
        );

        $this->_customOptions = array();
        $valueFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Option\ValueFactory',
            array(),
            array(),
            '',
            false
        );

        for ($i = 1; $i <= 3; $i++) {
            $option = $objectManagerHelper->getObject(
                'Magento\Catalog\Model\Product\Option',
                array('resource' => $this->_optionResource, 'optionValueFactory' => $valueFactoryMock)
            );
            $option->setIdFieldName('id');
            $option->setId($i);
            $option->setIsRequire(true);
            $this->_customOptions[\Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX .
                $i] = new \Magento\Framework\Object(
                array('value' => 'value')
            );
            $this->_product->addOption($option);
        }

        $this->_quoteItemOption = $this->getMock('Magento\Sales\Model\Quote\Item\Option', array(), array(), '', false);

        $this->_customOptions['info_buyRequest'] = $this->_quoteItemOption;

        $this->_product->expects($this->any())->method('getAllowOpenAmount')->will($this->returnValue(true));

        $this->_product->setSkipCheckRequiredOption(false);
        $this->_product->setCustomOptions($this->_customOptions);
    }

    public function testValidateEmptyFields()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(serialize(array()))
        );
        $this->_setGetGiftcardAmountsReturnEmpty();

        $this->_setStrictProcessMode(true);
        $this->setExpectedException(
            'Magento\Framework\Model\Exception',
            'Please specify all the required information.'
        );
        $this->_model->checkProductBuyState($this->_product);
    }

    public function testValidateEmptyAmount()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email'
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a gift card amount.');
    }

    public function testValidateMaxAmount()
    {
        $this->_preConditions();
        $this->_product->expects($this->once())->method('getOpenAmountMax')->will($this->returnValue(10));
        $this->_product->expects($this->once())->method('getOpenAmountMin')->will($this->returnValue(3));
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email',
                        'custom_giftcard_amount' => 15
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Gift Card max amount is ');
    }

    public function testValidateMinAmount()
    {
        $this->_preConditions();
        $this->_product->expects($this->once())->method('getOpenAmountMax')->will($this->returnValue(10));
        $this->_product->expects($this->once())->method('getOpenAmountMin')->will($this->returnValue(3));
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email',
                        'custom_giftcard_amount' => 2
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Gift Card min amount is ');
    }

    public function testValidateNoAllowedAmount()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email',
                        'giftcard_amount' => 7
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a gift card amount.');
    }

    public function testValidateRecipientName()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email',
                        'giftcard_amount' => 5
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a recipient name.');
    }

    public function testValidateSenderName()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_sender_email' => 'email',
                        'giftcard_amount' => 5
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a sender name.');
    }

    public function testValidateRecipientEmail()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_sender_email' => 'email',
                        'giftcard_amount' => 5
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a recipient email.');
    }

    public function testValidateSenderEmail()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(
                    array(
                        'giftcard_recipient_name' => 'name',
                        'giftcard_sender_name' => 'name',
                        'giftcard_recipient_email' => 'email',
                        'giftcard_amount' => 5
                    )
                )
            )
        );

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a sender email.');
    }

    public function testValidate()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(serialize(array()))
        );
        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_customOptions['info_buyRequest'] = $this->_quoteItemOption;
        $this->_product->setCustomOptions($this->_customOptions);

        $this->_setStrictProcessMode(false);
        $this->_model->checkProductBuyState($this->_product);
    }

    /**
     * Test _getCustomGiftcardAmount when rate is equal
     */
    public function testGetCustomGiftcardAmountForEqualRate()
    {
        $giftcardAmount = 11.54;
        $this->_mockModel(array('_isStrictProcessMode', '_getAmountWithinConstraints'));
        $this->_preConditions();
        $this->_setStrictProcessMode(false);
        $this->_setGetGiftcardAmountsReturnArray();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(array('custom_giftcard_amount' => $giftcardAmount, 'giftcard_amount' => 'custom'))
            )
        );
        $this->_model->expects(
            $this->once()
        )->method(
            '_getAmountWithinConstraints'
        )->with(
            $this->equalTo($this->_product),
            $this->equalTo($giftcardAmount),
            $this->equalTo(false)
        )->will(
            $this->returnValue($giftcardAmount)
        );
        $this->_model->checkProductBuyState($this->_product);
    }

    /**
     * Test _getCustomGiftcardAmount when current currency rate is not equal
     */
    public function testGetCustomGiftcardAmountForDifferentRate()
    {
        $giftcardAmount = 11.54;
        $storeRate = 2;
        $this->_store->expects($this->any())->method('getCurrentCurrencyRate')->will($this->returnValue($storeRate));
        $this->_mockModel(array('_isStrictProcessMode', '_getAmountWithinConstraints'));
        $this->_preConditions();
        $this->_setStrictProcessMode(false);
        $this->_setGetGiftcardAmountsReturnArray();
        $this->_quoteItemOption->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(
                serialize(array('custom_giftcard_amount' => $giftcardAmount, 'giftcard_amount' => 'custom'))
            )
        );
        $this->_model->expects(
            $this->once()
        )->method(
            '_getAmountWithinConstraints'
        )->with(
            $this->equalTo($this->_product),
            $this->equalTo($giftcardAmount / $storeRate),
            $this->equalTo(false)
        )->will(
            $this->returnValue($giftcardAmount)
        );
        $this->_model->checkProductBuyState($this->_product);
    }

    /**
     * Running validation with specified exception message
     *
     * @param string $exceptionMessage
     */
    protected function _runValidationWithExpectedException($exceptionMessage)
    {
        $this->_customOptions['info_buyRequest'] = $this->_quoteItemOption;

        $this->_product->setCustomOptions($this->_customOptions);

        $this->setExpectedException('Magento\Framework\Model\Exception', $exceptionMessage);
        $this->_model->checkProductBuyState($this->_product);
    }

    /**
     * Set getGiftcardAmount return value to empty array
     */
    protected function _setGetGiftcardAmountsReturnEmpty()
    {
        $this->_product->expects($this->once())->method('getGiftcardAmounts')->will($this->returnValue(array()));
    }

    /**
     * Set getGiftcardAmount return value
     */
    protected function _setGetGiftcardAmountsReturnArray()
    {
        $this->_product->expects(
            $this->once()
        )->method(
            'getGiftcardAmounts'
        )->will(
            $this->returnValue(array(array('website_value' => 5)))
        );
    }

    /**
     * Set strict mode
     *
     * @param bool $mode
     */
    protected function _setStrictProcessMode($mode)
    {
        $this->_model->expects($this->once())->method('_isStrictProcessMode')->will($this->returnValue((bool)$mode));
    }

    protected function _setAmountWithConstraints()
    {
        $this->_model->expects($this->once())->method('_getAmountWithinConstraints')->will($this->returnArgument(1));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
