<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for the controller tests
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
abstract class Magento_Test_TestCase_ControllerAbstract extends Magento_TestCase
{
    protected $_runCode     = '';
    protected $_runScope    = 'store';
    protected $_runOptions  = array();
    protected $_request;
    protected $_response;

    /**
     * Bootstrap instance getter
     *
     * @return Magento_Test_Bootstrap
     */
    protected function _getBootstrap()
    {
        return Magento_Test_Bootstrap::getInstance();
    }

    /**
     * Bootstrap application before eny test
     *
     * @return void
     */
    protected function setUp()
    {
        /**
         * Use run options from bootstrap
         */
        $this->_runOptions = $this->_getBootstrap()->getAppOptions();
        $this->_runOptions['request']   = $this->getRequest();
        $this->_runOptions['response']  = $this->getResponse();
    }

    /**
     * Run request
     *
     * @param string $uri
     */
    public function dispatch($uri)
    {
        $this->getRequest()->setRequestUri($uri);
        Mage::run($this->_runCode, $this->_runScope, $this->_runOptions);
    }

    /**
     * Request getter
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = new Magento_Test_Request();
        }
        return $this->_request;
    }

    /**
     * Response getter
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = new Magento_Test_Response();
        }
        return $this->_response;
    }

    /**
     * Assert that response is '404 Not Found'
     */
    public function assert404NotFound()
    {
        $this->assertEquals('noRoute', $this->getRequest()->getActionName());
        $this->assertContains('404 Not Found', $this->getResponse()->getBody());
    }

    /**
     * Assert that there is a redirect to expected URL.
     * Omit expected URL to check that redirect to wherever has been occurred.
     *
     * @param string|null $expectedUrl
     */
    public function assertRedirect($expectedUrl = null)
    {
        $this->assertTrue($this->getResponse()->isRedirect());
        if ($expectedUrl) {
            $this->assertContains(array(
                'name'    => 'Location',
                'value'   => $expectedUrl,
                'replace' => true,
            ), $this->getResponse()->getHeaders());
        }
    }

    /**
     * Assert that there is a expected match of redirect URL
     *
     * Omit expected URL to check that redirect to wherever has been occurred.
     *
     * @param string|null $matchUrl         Match URL on redirect
     * @param string $message               Custom error message
     */
    public function assertRedirectMatch($matchUrl = null, $message = '')
    {
        $messageAssert = $message ? $message : 'Response is not contain redirect header.';
        $this->assertTrue($this->getResponse()->isRedirect(), $messageAssert);

        if ($matchUrl) {
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ('Location' == $header['name'] && true === $header['replace']) {
                    if (!$message) {
                        $message = 'Expected redirect URL does not match URL in Location header.';
                    }
                    $this->assertContains($matchUrl, $header['value'], $message);
                    break;
                }
            }
        }
    }

    /**
     * Login to admin panel
     *
     * @param string|null $username     Identity
     * @param string|null $password     Credential
     * @return Magento_Test_ControllerTestCaseAbstract
     * @throws Magento_Test_Exception   Throw exception when admin user not found
     */
    public function loginToAdmin($username = null, $password = null)
    {
        /** @var $session Mage_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if (null === $username) {
            $username = TESTS_ADMIN_USERNAME;
        }
        if (null === $password) {
            $password = TESTS_ADMIN_PASSWORD;
        }
        if (!$session->isLoggedIn() || false !== ($user = $session->getUser())
            && $user->getUsername() != $username
        ) {
            /** @var $user Mage_User_Model_User */
            $user = Mage::getModel('Mage_User_Model_User');
            $user->login($username, $password);
            if ($user->getId()) {
                $session->setIsFirstPageAfterLogin(true);
                $session->setUser($user);
                /** @var $acl Mage_Admin_Model_Resource_Acl */
                $acl = Mage::getResourceModel('Mage_Admin_Model_Resource_Acl');
                $session->setAcl($acl->loadAcl());
            }

            if (!$session->isLoggedIn()) {
                throw new Magento_Test_Exception(
                    sprintf('Admin cannot logged with username "%s".', $username));
            }
        }
        return $this;
    }

    /**
     * Login to admin panel
     *
     * @param string|null $email        Identity
     * @param string|null $password     Credential
     * @return Magento_Test_ControllerTestCaseAbstract
     * @throws Magento_Test_Exception   Throw exception when admin user not found
     */
    public function loginToFrontend($email = null, $password = null)
    {
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('Mage_Customer_Model_Session');
        if (null === $email) {
            $email = TESTS_CUSTOMER_EMAIL;
        }
        if (null === $password) {
            $password = TESTS_CUSTOMER_PASSWORD;
        }
        if (!$session->isLoggedIn() || false !== ($user = $session->getCustomer())
            && $user->getEmail() != $email
        ) {
            /** @var $user Mage_Customer_Model_Customer */
            $user = Mage::getModel('Mage_Customer_Model_Customer');
            $user->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->authenticate($email, $password);

            if ($user->getId()) {
                $session->setCustomerAsLoggedIn($user);
            }

            if (!$session->isLoggedIn()) {
                throw new Magento_Test_Exception(
                    sprintf('Customer cannot logged with email "%s".', $email));
            }
        }
        return $this;
    }

    /**
     * Retrieve URI with admin secret key
     *
     * @param string $urlPath
     * @return string
     */
    protected function _getUrlPathWithSecretKey($urlPath)
    {
        $uri = Mage::getModel('Mage_Adminhtml_Model_Url')->getUrl($urlPath);
        // strip base URL if detected
        $baseUrl = rtrim(Mage::getBaseUrl(), '/');
        if (strpos($uri, $baseUrl) === 0) {
            $uri = substr($uri, strlen($baseUrl));
            return $uri;
        }
        return $uri;
    }
}
