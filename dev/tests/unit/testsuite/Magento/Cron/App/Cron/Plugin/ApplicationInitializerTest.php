<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\App\Cron\Plugin;

class ApplicationInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cron\App\Cron\Plugin\ApplicationInitializer
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sidResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->sidResolverMock = $this->getMock('\Magento\Session\SidResolverInterface', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\App\Cron', array(), array(), '', false);
        $this->model = new ApplicationInitializer(
            $this->appStateMock,
            $this->sidResolverMock
        );
    }

    public function testBeforeExecutePerformsRequiredChecks()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->sidResolverMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $this->model->beforeLaunch($this->subjectMock);
    }
}
