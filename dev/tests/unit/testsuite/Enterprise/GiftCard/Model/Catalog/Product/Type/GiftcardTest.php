<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_Catalog_Product_Type_GiftcardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_customOptions;

    /**
     * @var Magento_Catalog_Model_Resource_Product
     */
    protected $_productResource;

    /**
     * @var Magento_Catalog_Model_Resource_Product_Option
     */
    protected $_optionResource;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * @var Magento_Sales_Model_Quote_Item_Option
     */
    protected $_quoteItemOption;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_store = $this->getMock('Magento_Core_Model_Store', array('getCurrentCurrencyRate'), array(), '', false);
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
            'Enterprise_GiftCard_Helper_Data'        => $this->getMock(
                'Enterprise_GiftCard_Helper_Data', array(), array(), '', false, false
            ),
            'Magento_Core_Helper_Data'                  => $this->getMock(
                'Magento_Core_Helper_Data', array(), array(), '', false, false
            ),
            'Magento_Catalog_Helper_Data'               => $this->getMock(
                'Magento_Catalog_Helper_Data', array(), array(), '', false, false
            ),
            'Magento_Core_Helper_File_Storage_Database' => $this->getMock(
                'Magento_Core_Helper_File_Storage_Database', array(), array(), '', false, false
            )
        );

        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $locale = $this->getMock('Magento_Core_Model_Locale', array('getNumber'), array(), '', false);
        $locale->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $this->_model = $this->getMock(
            'Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard',
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
        $this->_productResource = $this->getMock('Magento_Catalog_Model_Resource_Product', array(), array(), '', false);
        $this->_optionResource = $this->getMock('Magento_Catalog_Model_Resource_Product_Option', array(), array(),
            '', false);

        $productCollection = $this->getMock('Magento_Catalog_Model_Resource_Product_Collection', array(), array(), '',
            false
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Magento_Catalog_Model_Product',
            array('resource' => $this->_productResource, 'resourceCollection' => $productCollection)
        );
        $this->_product = $this->getMock(
            'Magento_Catalog_Model_Product',
            array('getGiftcardAmounts', 'getAllowOpenAmount', 'getOpenAmountMax', 'getOpenAmountMin'),
            $arguments
        );

        $this->_customOptions = array();

        for ($i = 1; $i <= 3; $i++) {
            $option = $objectManagerHelper->getObject('Magento_Catalog_Model_Product_Option',
                array('resource' => $this->_optionResource)
            );
            $option->setIdFieldName('id');
            $option->setId($i);
            $option->setIsRequire(true);
            $this->_customOptions[Magento_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX . $i] = new Magento_Object(
                array('value' => 'value')
            );
            $this->_product->addOption($option);
        }

        $this->_quoteItemOption = $this->getMock('Magento_Sales_Model_Quote_Item_Option', array(), array(), '', false);

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
        $this->setExpectedException('Magento_Core_Exception', 'Please specify all the required information.');
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

        $this->setExpectedException('Magento_Core_Exception', $exceptionMessage);
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
