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

    protected function setUp()
    {
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);

        $helpers = array(
            'Enterprise_GiftCard_Helper_Data' => $this->getMock('Enterprise_GiftCard_Helper_Data'),
            'Mage_Core_Helper_Data' => $this->getMock('Mage_Core_Helper_Data'),
            'Mage_Catalog_Helper_Data' => $this->getMock('Mage_Catalog_Helper_Data'),
            'Mage_Core_Helper_File_Storage_Database' => $this->getMock('Mage_Core_Helper_File_Storage_Database')
        );

        foreach ($helpers as $helper) {
            $helper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));
        }

        $this->_model = new Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard(array(
            'store' => $store,
            'helpers' => $helpers
        ));
    }

    public function testCheckProductBuyState()
    {
        $productResource = $this->getMock('Mage_Catalog_Model_Resource_Product', array(), array(), '', false);
        $productOptionResource = $this->getMock('Mage_Catalog_Model_Resource_Product_Option', array(), array(), '', false);
        $product = new Mage_Catalog_Model_Product(array('resource' => $productResource));
        $product->setSkipCheckRequiredOption(false);

        $customOptions = array();

        for ($i = 1; $i <= 3; $i++) {
            $option = new Mage_Catalog_Model_Product_Option(array('resource' => $productOptionResource));
            $option->setIdFieldName('id');
            $option->setId($i);
            $option->setIsRequire(true);
            $customOptions[Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX . $i] = new Varien_Object(array('value'=>'lolo'));
            $product->addOption($option);
        }

        $quoteItemOption = $this->getMock('Mage_Sales_Model_Quote_Item_Option', array(), array(), '', false);

        $quoteItemOption->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(serialize(array('lolo'=>'qwerty'))));

        $customOptions['info_buyRequest'] = $quoteItemOption;//(array('value' => serialize(array('lolo'=>'qwerty'))));

        $product->setCustomOptions($customOptions);
        $this->_model->checkProductBuyState($product);

        $this->setExpectedException(
            'Mage_Core_Exception'//, 'The product has required options'
        );
    }

}
