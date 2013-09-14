<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Catalog_Product_Type_GiftcardTest extends PHPUnit_Framework_TestCase
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
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Sales\Model\Quote\Item\Option
     */
    protected $_quoteItemOption;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_store = $this->getMock('Magento\Core\Model\Store', array('getCurrentCurrencyRate'), array(), '', false);
        $this->_mockModel(array('_isStrictProcessMode'));
    }

    /**
     * Create model Mock
     *
     * @param $mockedMethods
     */
    protected function _mockModel($mockedMethods)
    {
        $helpers = array(
            'Magento\GiftCard\Helper\Data'        => $this->getMock(
                'Magento\GiftCard\Helper\Data', array(), array(), '', false, false
            ),
            'Magento\Core\Helper\Data'                  => $this->getMock(
                'Magento\Core\Helper\Data', array(), array(), '', false, false
            ),
            'Magento\Catalog\Helper\Data'               => $this->getMock(
                'Magento\Catalog\Helper\Data', array(), array(), '', false, false
            ),
            'Magento\Core\Helper\File\Storage\Database' => $this->getMock(
                'Magento\Core\Helper\File\Storage\Database', array(), array(), '', false, false
            )
        );

        $filesystem = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $locale = $this->getMock('Magento\Core\Model\Locale', array('getNumber'), array(), '', false);
        $locale->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $this->_model = $this->getMock(
            'Magento\GiftCard\Model\Catalog\Product\Type\Giftcard',
            $mockedMethods,
            array(
                $filesystem,
                array(
                    'store'     => $this->_store,
                    'helpers'   => $helpers,
                    'locale'    => $locale,
                )
            )
        );
    }

    protected function _preConditions()
    {
        $this->_store->expects($this->any())->method('getCurrentCurrencyRate')->will($this->returnValue(1));
        $this->_productResource = $this->getMock('Magento\Catalog\Model\Resource\Product', array(), array(), '', false);
        $this->_optionResource = $this->getMock('Magento\Catalog\Model\Resource\Product\Option', array(), array(),
            '', false);

        $productCollection = $this->getMock('Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '',
            false
        );

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Magento\Catalog\Model\Product',
            array('resource' => $this->_productResource, 'resourceCollection' => $productCollection)
        );
        $this->_product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getGiftcardAmounts', 'getAllowOpenAmount', 'getOpenAmountMax', 'getOpenAmountMin'),
            $arguments
        );

        $this->_customOptions = array();

        for ($i = 1; $i <= 3; $i++) {
            $option = $objectManagerHelper->getObject('Magento\Catalog\Model\Product\Option',
                array('resource' => $this->_optionResource)
            );
            $option->setIdFieldName('id');
            $option->setId($i);
            $option->setIsRequire(true);
            $this->_customOptions[\Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX . $i] =
                new \Magento\Object(array('value' => 'value')
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
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array())));
        $this->_setGetGiftcardAmountsReturnEmpty();

        $this->_setStrictProcessMode(true);
        $this->setExpectedException('Magento\Core\Exception', 'Please specify all the required information.');
        $this->_model->checkProductBuyState($this->_product);
    }

    public function testValidateEmptyAmount()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
            ))));

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a gift card amount.');
    }

    public function testValidateMaxAmount()
    {
        $this->_preConditions();
        $this->_product->expects($this->once())->method('getOpenAmountMax')->will($this->returnValue(10));
        $this->_product->expects($this->once())->method('getOpenAmountMin')->will($this->returnValue(3));
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'custom_giftcard_amount'    => 15,
            ))));

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Gift Card max amount is ');
    }

    public function testValidateMinAmount()
    {
        $this->_preConditions();
        $this->_product->expects($this->once())->method('getOpenAmountMax')->will($this->returnValue(10));
        $this->_product->expects($this->once())->method('getOpenAmountMin')->will($this->returnValue(3));
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'custom_giftcard_amount'    => 2,
            ))));

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Gift Card min amount is ');
    }

    public function testValidateNoAllowedAmount()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 7,
            ))));

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a gift card amount.');
    }

    public function testValidateRecipientName()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a recipient name.');
    }

    public function testValidateSenderName()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a sender name.');
    }

    public function testValidateRecipientEmail()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a recipient email.');
    }

    public function testValidateSenderEmail()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify a sender email.');
    }

    public function testValidate()
    {
        $this->_preConditions();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array())));
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
        $this->_mockModel(array('_isStrictProcessMode', '_getAmountWithinConstraints', ));
        $this->_preConditions();
        $this->_setStrictProcessMode(false);
        $this->_setGetGiftcardAmountsReturnArray();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'custom_giftcard_amount'    => $giftcardAmount,
                'giftcard_amount'           => 'custom',
            ))));
        $this->_model->expects($this->once())
            ->method('_getAmountWithinConstraints')
            ->with($this->equalTo($this->_product), $this->equalTo($giftcardAmount), $this->equalTo(false))
            ->will($this->returnValue($giftcardAmount));
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
        $this->_mockModel(array('_isStrictProcessMode', '_getAmountWithinConstraints', ));
        $this->_preConditions();
        $this->_setStrictProcessMode(false);
        $this->_setGetGiftcardAmountsReturnArray();
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'custom_giftcard_amount'    => $giftcardAmount,
                'giftcard_amount'           => 'custom',
            ))));
        $this->_model->expects($this->once())
            ->method('_getAmountWithinConstraints')
            ->with($this->equalTo($this->_product), $this->equalTo($giftcardAmount/$storeRate), $this->equalTo(false))
            ->will($this->returnValue($giftcardAmount));
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

        $this->setExpectedException('Magento\Core\Exception', $exceptionMessage);
        $this->_model->checkProductBuyState($this->_product);
    }

    /**
     * Set getGiftcardAmount return value to empty array
     */
    protected function _setGetGiftcardAmountsReturnEmpty()
    {
        $this->_product->expects($this->once())->method('getGiftcardAmounts')
            ->will($this->returnValue(array()));
    }

    /**
     * Set getGiftcardAmount return value
     */
    protected function _setGetGiftcardAmountsReturnArray()
    {
        $this->_product->expects($this->once())->method('getGiftcardAmounts')
            ->will($this->returnValue(array(array('website_value' => 5))));
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
