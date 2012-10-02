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
     * @var Mage_Catalog_Model_Resource_Product
     */
    protected $_productResource;

    /**
     * @var Mage_Catalog_Model_Resource_Product_Option
     */
    protected $_optionResource;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * @var Mage_Sales_Model_Quote_Item_Option
     */
    protected $_quoteItemOption;

    protected function setUp()
    {
        $store = $this->getMock('Mage_Core_Model_Store', array('getCurrentCurrencyRate'), array(), '', false);
        $store->expects($this->once())->method('getCurrentCurrencyRate')->will($this->returnValue(1));

        $helpers = array(
            'Enterprise_GiftCard_Helper_Data'        => $this->getMock('Enterprise_GiftCard_Helper_Data'),
            'Mage_Core_Helper_Data'                  => $this->getMock('Mage_Core_Helper_Data'),
            'Mage_Catalog_Helper_Data'               => $this->getMock('Mage_Catalog_Helper_Data'),
            'Mage_Core_Helper_File_Storage_Database' => $this->getMock('Mage_Core_Helper_File_Storage_Database')
        );

        foreach ($helpers as $helper) {
            $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        }

        $locale = new Varien_Object(array('number' => 100));

        $this->_model = $this->getMock(
            'Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard',
            array('_isStrictProcessMode'),
            array(array(
                'store'     => $store,
                'helpers'   => $helpers,
                'locale'    => $locale,
            ))
        );

        $this->_productResource = $this->getMock('Mage_Catalog_Model_Resource_Product', array(), array(), '', false);
        $this->_optionResource = $this->getMock('Mage_Catalog_Model_Resource_Product_Option', array(), array(),
            '', false);
        $this->_product = $this->getMock('Mage_Catalog_Model_Product',
            array('getGiftcardAmounts', 'getAllowOpenAmount', 'getOpenAmountMax', 'getOpenAmountMin'),
            array(array('resource' => $this->_productResource))
        );

        $this->_customOptions = array();

        for ($i = 1; $i <= 3; $i++) {
            $option = new Mage_Catalog_Model_Product_Option(array('resource' => $this->_optionResource));
            $option->setIdFieldName('id');
            $option->setId($i);
            $option->setIsRequire(true);
            $this->_customOptions[Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX . $i] = new Varien_Object(
                array('value' => 'value')
            );
            $this->_product->addOption($option);
        }

        $this->_quoteItemOption = $this->getMock('Mage_Sales_Model_Quote_Item_Option', array(), array(), '', false);

        $this->_customOptions['info_buyRequest'] = $this->_quoteItemOption;

        $this->_product->expects($this->any())->method('getAllowOpenAmount')->will($this->returnValue(true));

        $this->_product->setSkipCheckRequiredOption(false);
        $this->_product->setCustomOptions($this->_customOptions);
    }

    public function testValidateEmptyFields()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array())));
        $this->_setGetGiftcardAmountsReturnEmpty();

        $this->_setStrictProcessMode(true);
        $this->setExpectedException('Mage_Core_Exception', 'Please specify all the required information.');
        $this->_model->checkProductBuyState($this->_product);
    }

    public function testValidateEmptyAmount()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
            ))));

        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify Gift Card amount.');
    }

    public function testValidateMaxAmount()
    {
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
        $this->_runValidationWithExpectedException('Gift Card max amount is %s');
    }

    public function testValidateMinAmount()
    {
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
        $this->_runValidationWithExpectedException('Gift Card min amount is %s');
    }

    public function testValidateNoAllowedAmount()
    {
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
        $this->_runValidationWithExpectedException('Please specify Gift Card amount.');
    }

    public function testValidateRecipientName()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify recipient name.');
    }

    public function testValidateSenderName()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify sender name.');
    }

    public function testValidateRecipientEmail()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_sender_email'     => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify recipient email.');
    }

    public function testValidateSenderEmail()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array(
                'giftcard_recipient_name'   => 'name',
                'giftcard_sender_name'      => 'name',
                'giftcard_recipient_email'  => 'email',
                'giftcard_amount'           => 5,
            ))));

        $this->_setGetGiftcardAmountsReturnArray();
        $this->_setStrictProcessMode(true);
        $this->_runValidationWithExpectedException('Please specify sender email.');
    }

    public function testValidate()
    {
        $this->_quoteItemOption->expects($this->any())->method('getValue')
            ->will($this->returnValue(serialize(array())));
        $this->_setGetGiftcardAmountsReturnEmpty();
        $this->_customOptions['info_buyRequest'] = $this->_quoteItemOption;
        $this->_product->setCustomOptions($this->_customOptions);

        $this->_setStrictProcessMode(false);
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

        $this->setExpectedException('Mage_Core_Exception', $exceptionMessage);
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
}
