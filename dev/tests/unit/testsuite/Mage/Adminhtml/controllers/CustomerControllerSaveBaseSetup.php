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

require_once realpath(__DIR__ . '/../../../../../../../')
    . '/app/code/core/Mage/Adminhtml/controllers/CustomerController.php';

class Mage_Adminhtml_CustomerControllerSaveBaseSetup extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_CustomerController
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontCtrlMock;

    /**
     * @var array
     */
    protected $_formData;

    /**
     * @var array
     */
    protected $_postData;

    /**
     * Test customer id
     * @var int
     */
    protected $_customerId;

    public function setUp()
    {
        $this->_customerId = 1;

        $this->_initializeFormData();

        $this->_initializePostData();

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

    protected function _initializeFormData()
    {
        $this->_formData = array(
            'website_id' => 0,
            'group_id' => 1,
        );
    }

    protected function _initializePostData()
    {
        $this->_postData = array(
            'account' => $this->_formData,
            'subscription' => array(),
            'address' => array(
                '_template_' => 'test template',
            )
        );
    }

    protected function _initializeRequestMock()
    {
        $requestParamMap = array(
            array('back', false, true),
            array('customer_id', null, $this->_customerId),
        );
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_requestMock->expects($this->once())->method('getPost')->will($this->returnValue($this->_postData));
        $this->_requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($requestParamMap));
    }

    protected function _initializeResponseMock()
    {
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false, false);
        $this->_responseMock->expects($this->once())->method('setRedirect')->with($this->stringEndsWith('edit'));
    }

    protected function _initializeHelperMock()
    {
        $this->_helperMock = $this->getMock('Mage_Backend_Helper_Data', array('getUrl', '__'), array(), '', false);
        $this->_helperMock->expects($this->once())->method('getUrl')->will($this->returnArgument(0));
    }

    protected function _initializeFrontControllerMock()
    {
        $this->_frontCtrlMock = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
    }

    protected function _initializeTranslatorMock()
    {
        $this->_translatorMock = $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false);
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

    protected function _initializeControllerInstance()
    {
        $this->_model = new Mage_Adminhtml_CustomerController(
            $this->_requestMock,
            $this->_responseMock,
            $this->_getInvokeArgs()
        );
    }

    public function tearDown()
    {
        unset($this->_responseMock);
        unset($this->_requestMock);
        unset($this->_model);
        unset($this->_helperMock);
        unset($this->_frontCtrlMock);
        unset($this->_translatorMock);
    }
}
