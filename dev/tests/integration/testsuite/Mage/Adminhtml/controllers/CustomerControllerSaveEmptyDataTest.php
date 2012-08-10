<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__) . '/CustomerControllerSaveSetup.php';

class Mage_Adminhtml_CustomerControllerSaveEmptyDataTest extends Mage_Adminhtml_CustomerControllerSaveSetup
{
    public function setUp()
    {
        /** Initialize request mock */
        $this->_initializeRequestMock();

        /** Initialize response mock */
        $this->_initializeResponseMock();

        /** Initialize helper mock */
        $this->_initializeHelperMock();

        /** Initialize controller instance */
        $this->_initializeControllerInstance();
    }

    protected function _getInvokeArgs()
    {
        $invokeArgs = array(
            'areaCode' => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'helper' => $this->_helperMock
        );
        return $invokeArgs;
    }

    protected function _initializeRequestMock()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
    }

    protected function _initializeResponseMock()
    {
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_responseMock->expects($this->once())->method('setRedirect')->with($this->equalTo('*/customer'));
    }

    public function testSaveActionWithEmptyPostData()
    {
        $this->_requestMock->expects($this->once())->method('getPost')->will($this->returnValue(null));
        $this->_model->saveAction();
    }
}
