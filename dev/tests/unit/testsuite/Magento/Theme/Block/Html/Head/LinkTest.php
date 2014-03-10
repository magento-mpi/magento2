<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html\Head;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Html\Head\Link
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetService;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $context = $this->getMock('\Magento\View\Element\Template\Context', array(), array(), '', false );
        $this->_assetService = $this->getMock('\Magento\View\Asset\Service', array(), array(), '', false );

        $context->expects($this->once())
            ->method('getAssetService')
            ->will($this->returnValue($this->_assetService));

        $this->_block = $objectManagerHelper->getObject(
            '\Magento\Theme\Block\Html\Head\Link',
            array('context' => $context)
        );

        $this->_block->setData('url', 'urlValue');
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Magento\View\Element\Template', $this->_block);
    }

    public function testGetAsset()
    {
        $asset = $this->getMock('\Magento\View\Asset\Remote', array(), array(), '', false );

        $this->_assetService->expects($this->once())
            ->method('createRemoteAsset')
            ->with('urlValue', 'link')
            ->will($this->returnValue($asset));

        $this->assertSame($this->_block->getAsset(), $asset);
    }
}
