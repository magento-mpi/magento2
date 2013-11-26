<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Html;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Page\Block\Html\Header::getLogoSrc
     */
    public function testGetLogoSrc()
    {
        $context = $this->getMock('\Magento\Core\Block\Template\Context', array(), array(), '', false);
        $filesystem = $this->getMock('\Magento\Filesystem', array(), array(), '', false );
        $mediaDirectory = $this->getMock('\Magento\Filesystem\Directory\Read', array(), array(), '', false );
        $storeConfig = $this->getMock('Magento\Core\Model\Store\Config', array('getConfig'), array(), '', false);
        $helperFactory = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);
        $urlBuilder = $this->getMock('Magento\UrlInterface');
        $context->expects($this->once())
            ->method('getStoreConfig')
            ->will($this->returnValue($storeConfig));
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));
        $context->expects($this->once())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactory));

        $storeConfig->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('default/image.gif'));
        $urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));
        $mediaDirectory->expects($this->any())
            ->method('isFile')
            ->will($this->returnValue(true));

        $filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValue($mediaDirectory));
        $helper = $this->getMock('Magento\Core\Helper\File\Storage\Database',
            array('checkDbUsage'), array(), '', false, false
        );
        $helper->expects($this->once())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $helperFactory->expects($this->once())
            ->method('get')
            ->will($this->returnValue($helper));
        $context->expects($this->once())
            ->method('getFilesystem')
            ->will($this->returnValue($filesystem));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $arguments = array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
            'helperFactory' => $helperFactory,
            'context' => $context
        );
        $block = $objectManager->getObject('Magento\Page\Block\Html\Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $block->getLogoSrc());
    }
}
