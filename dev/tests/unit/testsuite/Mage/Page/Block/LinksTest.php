<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Page_Block_LinksTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    /** @var Mage_Page_Block_Links */
    protected $_block;

    /** @var Mage_Core_Block_Template_Context */
    protected $_context;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        /** @var  Mage_Core_Block_Template_Context $context */
        $this->_context = $this->_objectManagerHelper->getObject('Mage_Core_Block_Template_Context');

        /** @var Mage_Page_Block_Links $block */
        $this->_block = $this->_objectManagerHelper->getObject(
            'Mage_Page_Block_Links',
            array(
                'context' => $this->_context,
            )
        );
    }

    public function testGetLinks()
    {
        $blocks = array(0 => 'blocks');
        $name = 'test_name';
        $this->_context->getLayout()
            ->expects($this->once())
            ->method('getChildBlocks')
            ->with($name)
            ->will($this->returnValue($blocks));
        $this->_block->setNameInLayout($name);
        $this->assertEquals($blocks,   $this->_block->getLinks());
    }

    public function testRenderLink()
    {
        $blockHtml = 'test';
        $name = 'test_name';
        $this->_context->getLayout()->expects($this->once())->method('renderElement')->with($name)->will(
            $this->returnValue($blockHtml)
        );

        /** @var Mage_Core_Block_Abstract $link */
        $link = $this->getMockBuilder('Mage_Core_Block_Abstract')->disableOriginalConstructor()->getMock();
        $link->expects($this->once())->method('getNameInLayout')->will(
            $this->returnValue($name)
        );
        $this->assertEquals($blockHtml, $this->_block->renderLink($link));
    }
} 