<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_JobNotification_Model_Grid_Massaction_ItemUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_JobNotification_Model_Grid_Massaction_ItemUpdater
     */
    private $_model;

    /**
     * @var Magento_AuthorizationInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $_authorization;

    protected function setUp()
    {
        $this->_authorization = $this->getMock('Magento_AuthorizationInterface');
        $this->_model = new Saas_JobNotification_Model_Grid_Massaction_ItemUpdater($this->_authorization);
    }

    /**
     * @param array $expectedResult
     * @param array $argument
     * @param array $acl
     * @dataProvider updateDataProvider
     */
    public function testUpdate(array $expectedResult, array $argument, array $acl)
    {
        $this->_authorization->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValueMap($acl));
        $this->assertEquals($expectedResult, $this->_model->update($argument));
    }

    public function updateDataProvider()
    {
        $argument = array(
            'mark_as_read' => 1,
            'remove' => 1,
        );
        return array(
            array(
                array(),
                $argument,
                array(
                    array('Saas_JobNotification::notification_action_markread', null, false),
                    array('Saas_JobNotification::notification_action_remove', null, false),
                )
            ),
            array(
                array(
                    'mark_as_read' => 1,
                ),
                $argument,
                array(
                    array('Saas_JobNotification::notification_action_markread', null, true),
                    array('Saas_JobNotification::notification_action_remove', null, false),
                )
            ),
            array(
                array(
                    'remove' => 1,
                ),
                $argument,
                array(
                    array('Saas_JobNotification::notification_action_markread', null, false),
                    array('Saas_JobNotification::notification_action_remove', null, true),
                )
            ),
            array(
                $argument,
                $argument,
                array(
                    array('Saas_JobNotification::notification_action_markread', null, true),
                    array('Saas_JobNotification::notification_action_remove', null, true),
                )
            ),
        );
    }
}
