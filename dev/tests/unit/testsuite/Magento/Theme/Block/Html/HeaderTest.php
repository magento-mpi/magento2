<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
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
        $storeConfig = $this->getMock('Magento\Core\Model\Store\Config', array('getConfig'), array(), '', false);
        $storeConfig->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('default/image.gif'));

        $urlBuilder = $this->getMock('Magento\UrlInterface');
        $urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $helper = $this->getMock('Magento\Core\Helper\File\Storage\Database',
            array('checkDbUsage'), array(), '', false, false
        );
        $helper->expects($this->once())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $dirsMock = $this->getMock('Magento\App\Dir', array('getDir'), array(), '', false);
        $dirsMock->expects($this->any())
            ->method('getDir')
            ->with(\Magento\App\Dir::MEDIA)
            ->will($this->returnValue(__DIR__ . DIRECTORY_SEPARATOR . '_files'));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $arguments = array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
            'fileStorageHelper' => $helper,
            'dirs' => $dirsMock
        );
        $block = $objectManager->getObject('Magento\Theme\Block\Html\Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $block->getLogoSrc());
    }
}
