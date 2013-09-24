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
 * Test class for \Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset\Options
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var \Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset\Options
     */
    protected $_optionsBlock;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Option
     */
    protected $_optionResource;

    protected function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_optionResource = $this->getMock('Magento\Catalog\Model\Resource\Product\Option',
            array(), array(), '', false);
    }

    public function testGetOptionHtml()
    {
        $layout = $this->getMock('Magento\Core\Model\Layout', array('getChildName', 'getBlock', 'renderElement'),
            array(), '', false);
        $context = $this->_objectHelper->getObject('Magento\Core\Block\Template\Context', array(
            'layout' => $layout
        ));
        $option = $this->_objectHelper->getObject('Magento\Catalog\Model\Product\Option',
            array('resource' => $this->_optionResource)
        );
        $dateBlock = $this->getMock('Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset\Options',
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
            'Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset\Options',
            array(
                'context' => $context,
                'option' => $option,
            )
        );
        $this->_optionsBlock->setProduct(
            $this->_objectHelper->getObject(
                'Magento\Catalog\Model\Product',
                array(
                    'collectionFactory' => $this->getMock(
                        'Magento\Data\CollectionFactory',
                        array(),
                        array(),
                        '',
                        false
                    )
                )
            )
        );

        $option = $this->_objectHelper->getObject('Magento\Catalog\Model\Product\Option',
            array('resource' => $this->_optionResource)
        );
        $option->setType('date');
        $this->assertEquals('html', $this->_optionsBlock->getOptionHtml($option));
    }
}
