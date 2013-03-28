<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Backup_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Saas_Backup_Model_Observer
     */
    protected $_modelBackupObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http');
        $this->_saasHelperMock = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelBackupObserver = $objectManagerHelper->getObject('Saas_Backup_Model_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_saasHelperMock,
        ));
    }

    public function testLimitBackupFunctionality()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('system_backup'));
        $this->_saasHelperMock->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelBackupObserver->limitBackupFunctionality($this->_eventObserverMock);
    }
}
