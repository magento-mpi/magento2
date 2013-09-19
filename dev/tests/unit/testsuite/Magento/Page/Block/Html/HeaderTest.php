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
        $storeConfig = $this->getMock('Magento\Core\Model\Store\Config', array('getConfig'));
        $storeConfig->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('default/image.gif'));

        $urlBuilder = $this->getMock('Magento\Core\Model\Url', array('getBaseUrl'), array(), '', false);
        $urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $helper = $this->getMock('Magento\Core\Helper\File\Storage\Database',
            array('checkDbUsage'), array(), '', false, false
        );
        $helper->expects($this->once())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $helperFactory = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);
        $helperFactory->expects($this->once())
            ->method('get')
            ->will($this->returnValue($helper));

        $dirsMock = $this->getMock('Magento\Core\Model\Dir', array('getDir'), array(), '', false);
        $dirsMock->expects($this->any())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::MEDIA)
            ->will($this->returnValue(__DIR__ . DIRECTORY_SEPARATOR . '_files'));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $arguments = array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
            'helperFactory' => $helperFactory,
            'dirs' => $dirsMock
        );
        $block = $objectManager->getObject('Magento\Page\Block\Html\Header', $arguments);

        $this->assertEquals('http://localhost/pub/media/logo/default/image.gif', $block->getLogoSrc());
    }
}
