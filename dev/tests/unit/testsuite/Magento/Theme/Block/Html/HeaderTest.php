<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Theme\Block\Html\Header::getLogoSrc
     */
    public function testGetLogoSrc()
    {
        $filesystem = $this->getMock('\Magento\Framework\Filesystem', array(), array(), '', false);
        $mediaDirectory = $this->getMock('\Magento\Framework\Filesystem\Directory\Read', array(), array(), '', false);
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $urlBuilder = $this->getMock('Magento\Framework\UrlInterface');

        $scopeConfig->expects($this->once())->method('getValue')->will($this->returnValue('default/image.gif'));
        $urlBuilder->expects(
            $this->once()
        )->method(
            'getBaseUrl'
        )->will(
            $this->returnValue('http://localhost/pub/media/')
        );
        $mediaDirectory->expects($this->any())->method('isFile')->will($this->returnValue(true));

        $filesystem->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($mediaDirectory));
        $helper = $this->getMock(
            'Magento\Core\Helper\File\Storage\Database',
            array('checkDbUsage'),
            array(),
            '',
            false,
            false
        );
        $helper->expects($this->once())->method('checkDbUsage')->will($this->returnValue(false));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $arguments = array(
            'scopeConfig' => $scopeConfig,
            'urlBuilder' => $urlBuilder,
            'fileStorageHelper' => $helper,
            'filesystem' => $filesystem
        );
        $block = $objectManager->getObject('Magento\Theme\Block\Html\Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $block->getLogoSrc());
    }
}
