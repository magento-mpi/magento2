<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__) . '/CustomerControllerSaveBaseSetup.php';

class Mage_Adminhtml_CustomerControllerSaveEmptyDataTest extends Mage_Adminhtml_CustomerControllerSaveBaseSetup
{
    public function setUp()
    {
        /** Initialize request mock */
        $this->_initializeRequestMock();

        /** Initialize response mock */
        $this->_initializeResponseMock();

        /** Initialize helper mock */
        $this->_initializeHelperMock();

        /** Initialize front controller mock */
        $this->_initializeFrontControllerMock();

        /** Initialize translator mock */
        $this->_initializeTranslatorMock();

        /** Initialize controller instance */
        $this->_initializeControllerInstance();
    }

    protected function _getInvokeArgs()
    {
        $invokeArgs = array(
            'areaCode' => 'adminhtml',
            'helper' => $this->_helperMock,
            'session' => '',
            'registry' => '',
            'acl' => '',
            'eventManager' => '',
            'frontController' => $this->_frontCtrlMock,
            'translator' => $this->_translatorMock,
        );
        return $invokeArgs;
    }

    protected function _initializeRequestMock()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false, false);
    }

    protected function _initializeResponseMock()
    {
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false, false);
        $this->_responseMock->expects($this->once())->method('setRedirect')->with($this->equalTo('*/customer'));
    }

    public function testSaveActionWithEmptyPostData()
    {
        $this->_requestMock->expects($this->once())->method('getPost')->will($this->returnValue(null));
        $this->_model->saveAction();
    }
}
