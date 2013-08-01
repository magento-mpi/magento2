<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
     */
    protected $_optionsBlock;

    /**
     * @var Mage_Catalog_Model_Resource_Product_Option
     */
    protected $_optionResource;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_optionResource = $this->getMock('Mage_Catalog_Model_Resource_Product_Option',
            array(), array(), '', false);
    }

    public function testGetOptionHtml()
    {
        $layout = $this->getMock('Mage_Core_Model_Layout', array('getChildName', 'getBlock', 'renderElement'),
            array(), '', false);
        $context = $this->_objectHelper->getObject('Mage_Core_Block_Template_Context', array(
            'layout' => $layout
        ));
        $option = $this->_objectHelper->getObject('Mage_Catalog_Model_Product_Option',
            array('resource' => $this->_optionResource)
        );
        $dateBlock = $this->getMock('Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options',
            array('setSkipJsReloadPrice'), array('context' => $context, 'option' => $option), '', false);
        $dateBlock->expects($this->any())
            ->method('setSkipJsReloadPrice')->will($this->returnValue($dateBlock));

        $layout->expects($this->any())
            ->method('getChildName')->will($this->returnValue('date'));
        $layout->expects($this->any())
            ->method('getBlock')->with('date')->will($this->returnValue($dateBlock));
        $layout->expects($this->any())
            ->method('renderElement')->with('date', false)->will($this->returnValue('html'));

        $this->_optionsBlock = new Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options($context, $option);
        $this->_optionsBlock->setProduct($this->_objectHelper->getObject('Mage_Catalog_Model_Product'));

        $option = $this->_objectHelper->getObject('Mage_Catalog_Model_Product_Option',
            array('resource' => $this->_optionResource)
        );
        $option->setType('date');
        $this->assertEquals('html', $this->_optionsBlock->getOptionHtml($option));
    }
}
