<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Plugin;

class CustomerRegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Invitation\Model\Plugin\CustomerRegistration
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invitationConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invitationHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->_invitationConfig = $this->getMock('Magento\Invitation\Model\Config', array(), array(), '', false);
        $this->_invitationHelper = $this->getMock('Magento\Invitation\Helper\Data', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\Customer\Model\Registration', array(), array(), '', false);
        $this->_model = new \Magento\Invitation\Model\Plugin\CustomerRegistration(
            $this->_invitationConfig,
            $this->_invitationHelper
        );
    }

    public function testAfterIsRegistrationIsAllowedRestrictsRegistrationIfInvitationIsRequired()
    {
        $this->_invitationConfig->expects($this->any())->method('isEnabledOnFront')->will($this->returnValue(true));
        $this->_invitationConfig->expects(
            $this->any()
        )->method(
            'getInvitationRequired'
        )->will(
            $this->returnValue(true)
        );
        $this->_invitationHelper->expects($this->once())->method('isRegistrationAllowed')->with(true);

        $this->assertFalse($this->_model->afterIsAllowed($this->subjectMock, true));
    }
}
