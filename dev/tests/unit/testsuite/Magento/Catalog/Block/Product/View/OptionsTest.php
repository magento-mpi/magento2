<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Block_Product_View_Options
 */
class Magento_Catalog_Block_Catalog_Product_View_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectHelper;
    /**
     * @var Magento_Catalog_Block_Product_View_Options
     */
    protected $_optionsBlock;

    /**
     * @var Magento_Catalog_Model_Resource_Product_Option
     */
    protected $_optionResource;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_Test_Helper_ObjectManager($this);
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
        $option = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Option',
            array('resource' => $this->_optionResource)
        );
        $dateBlock = $this->getMock('Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options',
            array('setProduct'), array('context' => $context, 'option' => $option), '', false);
        $dateBlock->expects($this->any())
            ->method('setProduct')->will($this->returnValue($dateBlock));

        $layout->expects($this->any())
            ->method('getChildName')->will($this->returnValue('date'));
        $layout->expects($this->any())
            ->method('getBlock')->with('date')->will($this->returnValue($dateBlock));
        $layout->expects($this->any())
            ->method('renderElement')->with('date', false)->will($this->returnValue('html'));

        $this->_optionsBlock = new Magento_Catalog_Block_Product_View_Options($context, $option);
        $this->_optionsBlock->setProduct($this->_objectHelper->getObject('Magento_Catalog_Model_Product'));

        $option = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Option',
            array('resource' => $this->_optionResource)
        );
        $option->setType('date');
        $this->assertEquals('html', $this->_optionsBlock->getOptionHtml($option));
    }
}
