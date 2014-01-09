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
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    protected function setUp()
    {
        $this->_applicationMock = $this->getMock('Magento\AppInterface');
        $this->_model = new ApplicationInitializer(
            $this->_applicationMock
        );
    }

    public function testBeforeExecutePerformsRequiredChecks()
    {
        $this->_applicationMock->expects($this->once())->method('requireInstalledInstance');
        $this->_applicationMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $this->_model->beforeExecute(array());
    }
}
