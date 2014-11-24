<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Plugin;

class CustomerRegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Plugin\CustomerRegistration
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $restrictionConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->restrictionConfig = $this->getMock('Magento\WebsiteRestriction\Model\ConfigInterface');
        $this->subjectMock = $this->getMock('Magento\Customer\Model\Registration', array(), array(), '', false);
        $this->model = new \Magento\WebsiteRestriction\Model\Plugin\CustomerRegistration($this->restrictionConfig);
    }

    public function testAfterIsRegistrationIsAllowedRestrictsRegistrationIfRestrictionModeForbidsIt()
    {
        $storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $this->restrictionConfig->expects(
            $this->any()
        )->method(
            'isRestrictionEnabled'
        )->will(
            $this->returnValue(true)
        );
        $this->restrictionConfig->expects(
            $this->once()
        )->method(
            'getMode'
        )->will(
            $this->returnValue(\Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE)
        );
        $this->assertFalse($this->model->afterIsAllowed($this->subjectMock, true));
    }
}
