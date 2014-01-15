<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Captcha\Helper\Adminhtml;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Captcha\Helper\Adminhtml\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * setUp
     */
    protected function setUp()
    {
        $backendConfig = $this->getMockBuilder('Magento\Backend\App\ConfigInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getValue', 'setValue', 'reinit', 'getFlag'))
            ->getMock();
        $backendConfig->expects($this->any())
            ->method('getValue')
            ->with('admin/captcha/qwe')
            ->will($this->returnValue('1'));

        $filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $directoryMock = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);

        $filesystemMock->expects($this->any())->method('getDirectoryWrite')->will($this->returnValue($directoryMock));
        $directoryMock->expects($this->any())->method('getAbsolutePath')->will($this->returnArgument(0));

        $this->_model = new \Magento\Captcha\Helper\Adminhtml\Data(
            $this->getMock('Magento\App\Helper\Context', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Config', array(), array(), '', false),
            $filesystemMock,
            $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false),
            $backendConfig
        );
    }

    public function testGetConfig()
    {
        $this->assertEquals('1', $this->_model->getConfig('qwe'));
    }

    /**
     * @covers \Magento\Captcha\Helper\Adminhtml\Data::_getWebsiteCode
     */
    public function testGetWebsiteId()
    {
        $this->assertStringEndsWith('/admin/', $this->_model->getImgDir());
    }
}
