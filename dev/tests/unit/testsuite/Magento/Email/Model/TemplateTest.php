<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Email\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTypeDataProvider
     * @param string $templateType
     * @param int $expectedResult
     */
    public function testGetType($templateType, $expectedResult)
    {
        $emailConfig = $this->getMockBuilder('\Magento\Email\Model\Template\Config')
            ->setMethods(array('getTemplateType'))
            ->disableOriginalConstructor()
            ->getMock();
        $emailConfig->expects($this->once())
            ->method('getTemplateType')
            ->will($this->returnValue($templateType));
        /** @var \Magento\Email\Model\Template $model */
        $model = $this->getMockBuilder('Magento\Email\Model\Template')
            ->setMethods(array('_init'))
            ->setConstructorArgs(array(
                $this->getMock('Magento\Model\Context', array(), array(), '', false),
                $this->getMock('Magento\Core\Model\View\Design', array(), array(), '', false),
                $this->getMock('Magento\Registry', array(), array(), '', false),
                $this->getMock('Magento\Core\Model\App\Emulation', array(), array(), '', false),
                $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false),
                $this->getMock('Magento\App\Filesystem', array(), array(), '', false),
                $this->getMock('Magento\View\Url', array(), array(), '', false),
                $this->getMock('Magento\View\FileSystem', array(), array(), '', false),
                $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
                $this->getMock('Magento\App\ConfigInterface', array(), array(), '', false),
                $this->getMock('Magento\Email\Model\Template\FilterFactory', array(), array(), '', false),
                $emailConfig,
                array('template_id' => 10),
            ))
            ->getMock();
        $this->assertEquals($expectedResult, $model->getType());
    }

    public function getTypeDataProvider()
    {
        return array(
            array('text', 1),
            array('html', 2),
        );
    }
}
