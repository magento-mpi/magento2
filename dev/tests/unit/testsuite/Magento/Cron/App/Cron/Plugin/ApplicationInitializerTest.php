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
    protected $applicationMock;

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
        $this->applicationMock = $this->getMock('Magento\AppInterface');
        $this->sidResolverMock = $this->getMock('\Magento\Session\SidResolverInterface', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\App\Cron', array(), array(), '', false);
        $this->model = new ApplicationInitializer(
            $this->applicationMock,
            $this->sidResolverMock
        );
    }

    public function testBeforeExecutePerformsRequiredChecks()
    {
        $this->applicationMock->expects($this->once())->method('requireInstalledInstance');
        $this->sidResolverMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $this->model->beforeLaunch($this->subjectMock);
    }
}
