<?php
/**
 * Smoke test suite for API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Core_Mage_Api_SmokeTest extends Mage_Selenium_TestCase
{
    protected static $_apiCredentials;

    protected static $_soapClient;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Create SOAP API User/Role
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $roleData = $this->loadDataSet('ApiSoapRoles', 'api_soap_role_new');
        $userData = $this->loadDataSet('ApiSoapUsers', 'new_api_soap_users_create');
        $this->apiHelper()->createApiSoapRole($roleData);
        $this->apiHelper()->createApiSoapUser($userData);
        $this->apiHelper()->setApiSoapRole($roleData, $userData);

        self::$_apiCredentials = $userData;
    }

    /**
     * @test
     */
    public function loginClient()
    {
        $this->_getSessionId();
    }

    /**
     * @test
     * @depends loginClient
     */
    public function apiRoutingTest()
    {
        $sessionId = $this->_getSessionId();

        $attributeList = self::$_soapClient->catalogCategoryAttributeList(array('sessionId' => $sessionId));
        $this->assertInternalType('array', $attributeList, 'Incorrect received type');

        $attributeList = self::$_soapClient->storeInfo(array('sessionId' => $sessionId));
        $this->assertInternalType('array', $attributeList, 'Incorrect received type');

        $this->setExpectedException('SoapFault', 'Requested store view not found.', 'Exception is absent');
        self::$_soapClient->storeInfo(array('sessionId' => 'InavlidSessionId'));
    }


    /**
     * <p>Retrieve session ID</p>
     *
     * @return string
     */
    protected function _getSessionId()
    {
        $response = $this->_getSoapClient()->login(
            array(
                'username' => self::$_apiCredentials['api_user_name'],
                'apiKey' => self::$_apiCredentials['api_user_api_key']
            )
        );
        $sessionId = $response->result;
        $this->assertInternalType('string', $sessionId, 'Unable to login');

        return $sessionId;
    }

    /**
     * <p>Create SOAP Client</p>
     *
     * @return SoapClient
     */
    protected function _getSoapClient()
    {
        $wsdl = str_replace('index.php/backend/admin/', 'api/soap_wsi?wsdl', $this->getConfigHelper()->getBaseUrl());
        if (!isset(self::$_soapClient)) {
            self::$_soapClient = new SoapClient($wsdl, array('soap_version'   => SOAP_1_2));
        }
        return self::$_soapClient;
    }

}