<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Product_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Block\Product\View|null
     */
    protected $_block = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|null
     */
    protected $_urlBuilder = null;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_urlBuilder = $this->getMockForAbstractClass('\Magento\Core\Model\UrlInterface');
        $args = array('urlBuilder' => $this->_urlBuilder);
        $this->_block = $helper->getObject('\Magento\GiftRegistry\Block\Product\View', $args);
    }

    /**
     * @param string $options
     * @param string|null $expectedTemplate
     * @dataProvider setGiftRegistryTemplateDataProvider
     */
    public function testSetGiftRegistryTemplate($options, $expectedTemplate)
    {
        $request = $this->_block->getRequest();
        $request->expects($this->any())
            ->method('getParam')
            ->with('options')
            ->will($this->returnValue($options));
        $childBlock = $this->getMockForAbstractClass('\Magento\Core\Block\AbstractBlock', array(), '', false);
        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->_block->setLayout($layout);
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('test')
            ->will($this->returnValue($childBlock));
        $this->_block->setGiftRegistryTemplate('test', 'template.phtml');
        $actualTemplate = $childBlock->getTemplate();
        $this->assertSame($expectedTemplate, $actualTemplate);
    }

    /**
     * @return array
     */
    public function setGiftRegistryTemplateDataProvider()
    {
        return array(
            'no options' => array(
                'some other option', null
            ),
            'with options' => array(
                \Magento\GiftRegistry\Block\Product\View::FLAG, 'template.phtml'
            ),
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Could not find block 'test'
     */
    public function testSetGiftRegistryTemplateNoBlock()
    {
        $this->_block->setGiftRegistryTemplate('test', 'template.phtml');
    }

    public function testSetGiftRegistryUrl()
    {
        $this->_urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('some_url'));
        $request = $this->_block->getRequest();
        $valueMap = array(
            array('options', null, \Magento\GiftRegistry\Block\Product\View::FLAG),
            array('entity', null, 'any'),
        );
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($valueMap));
        $childBlock = $this->getMockForAbstractClass('\Magento\Core\Block\AbstractBlock', array(), '', false);
        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->_block->setLayout($layout);
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('test')
            ->will($this->returnValue($childBlock));
        $this->_block->setGiftRegistryUrl('test');
        $actualUrl = $childBlock->getAddToGiftregistryUrl();
        $this->assertSame('some_url', $actualUrl);
    }

    public function testSetGiftRegistryUrlNoOptions()
    {
        $childBlock = $this->getMockForAbstractClass('\Magento\Core\Block\AbstractBlock', array(), '', false);
        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->_block->setLayout($layout);
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('test')
            ->will($this->returnValue($childBlock));
        $this->_block->setGiftRegistryUrl('test');
        $actualUrl = $childBlock->getGiftRegistryUrl();
        $this->assertNull($actualUrl);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Could not find block 'test'
     */
    public function testSetGiftRegistryUrlNoBlock()
    {
        $this->_block->setGiftRegistryUrl('test');
    }
}
