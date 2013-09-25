<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
     */
    protected $_optionsBlock;

    /**
     * @var Magento_Catalog_Model_Resource_Product_Option
     */
    protected $_optionResource;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_optionResource = $this->getMock('Magento_Catalog_Model_Resource_Product_Option',
            array(), array(), '', false);
    }

    public function testGetOptionHtml()
    {
        $layout = $this->getMock('Magento_Core_Model_Layout', array('getChildName', 'getBlock', 'renderElement'),
            array(), '', false);
        $context = $this->_objectHelper->getObject('Magento_Core_Block_Template_Context', array(
            'layout' => $layout
        ));
        $optionFactoryMock = $this->getMock('Magento_Catalog_Model_Product_Option_ValueFactory', array('create'),
            array(), '', false);

        $option = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Option', array(
            'resource' => $this->_optionResource,
            'optionValueFactory' => $optionFactoryMock,
        ));
        $dateBlock = $this->getMock('Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options',
            array('setSkipJsReloadPrice'), array('context' => $context, 'option' => $option), '', false);
        $dateBlock->expects($this->any())
            ->method('setSkipJsReloadPrice')->will($this->returnValue($dateBlock));

        $layout->expects($this->any())
            ->method('getChildName')->will($this->returnValue('date'));
        $layout->expects($this->any())
            ->method('getBlock')->with('date')->will($this->returnValue($dateBlock));
        $layout->expects($this->any())
            ->method('renderElement')->with('date', false)->will($this->returnValue('html'));

        $this->_optionsBlock = $this->_objectHelper->getObject(
            'Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options',
            array(
                'context' => $context,
                'option' => $option,
            )
        );

        $itemOptionFactoryMock = $this->getMock('Magento_Catalog_Model_Product_Configuration_Item_OptionFactory',
            array('create'), array(), '', false);
        $stockItemFactoryMock = $this->getMock('Magento_CatalogInventory_Model_Stock_ItemFactory',
            array('create'), array(), '', false);
        $productFactoryMock = $this->getMock('Magento_Catalog_Model_ProductFactory',
            array('create'), array(), '', false);
        $categoryFactoryMock = $this->getMock('Magento_Catalog_Model_CategoryFactory',
            array('create'), array(), '', false);

        $this->_optionsBlock->setProduct(
            $this->_objectHelper->getObject(
                'Magento_Catalog_Model_Product',
                array(
                    'collectionFactory' => $this->getMock(
                        'Magento_Data_CollectionFactory',
                        array(),
                        array(),
                        '',
                        false
                    ),
                    'itemOptionFactory' => $itemOptionFactoryMock,
                    'stockItemFactory' => $stockItemFactoryMock,
                    'productFactory' => $productFactoryMock,
                    'categoryFactory' => $categoryFactoryMock,
                )
            )
        );

        $option = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Option', array(
            'resource' => $this->_optionResource,
            'optionValueFactory' => $optionFactoryMock,
        ));
        $option->setType('date');
        $this->assertEquals('html', $this->_optionsBlock->getOptionHtml($option));
    }
}
