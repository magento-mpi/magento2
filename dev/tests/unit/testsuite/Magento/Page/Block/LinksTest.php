<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Block;

class LinksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    /** @var \Magento\Page\Block\Links */
    protected $_block;

    /** @var \Magento\Core\Block\Template\Context */
    protected $_context;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var  \Magento\Core\Block\Template\Context $context */
        $this->_context = $this->_objectManagerHelper->getObject('Magento\Core\Block\Template\Context');

        /** @var \Magento\Page\Block\Links $block */
        $this->_block = $this->_objectManagerHelper->getObject(
            'Magento\Page\Block\Links',
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
        $this->assertEquals($blocks, $this->_block->getLinks());
    }

    public function testRenderLink()
    {
        $blockHtml = 'test';
        $name = 'test_name';
        $this->_context->getLayout()->expects($this->once())->method('renderElement')->with($name)
            ->will($this->returnValue($blockHtml));

        /** @var \Magento\Core\Block\AbstractBlock $link */
        $link = $this->getMockBuilder('Magento\Core\Block\AbstractBlock')->disableOriginalConstructor()->getMock();
        $link->expects($this->once())
            ->method('getNameInLayout')
            ->will($this->returnValue($name));

        $this->assertEquals($blockHtml, $this->_block->renderLink($link));
    }
} 
