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
abstract class Magento_TestFramework_TestCase_ControllerAbstract extends PHPUnit_Framework_TestCase
{
    protected $_runCode     = '';
    protected $_runScope    = 'store';
    protected $_runOptions  = array();

    /**
     * @var Magento_TestFramework_Request
     */
    protected $_request;

    /**
     * @var Magento_TestFramework_Response
     */
    protected $_response;

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * Whether absence of session error messages has to be asserted automatically upon a test completion
     *
     * @var bool
     */
    protected $_assertSessionErrors = false;

    /**
     * Bootstrap instance getter
     *
     * @return Magento_TestFramework_Helper_Bootstrap
     */
    protected function _getBootstrap()
    {
        return Magento_TestFramework_Helper_Bootstrap::getInstance();
    }

    /**
     * Bootstrap application before any test
     */
    protected function setUp()
    {
        $this->_assertSessionErrors = false;
        $this->_objectManager = Magento_TestFramework_ObjectManager::getInstance();
        $this->_objectManager->configure(array(
            'preferences' => array(
                'Magento\Core\Controller\Request\Http' => 'Magento_TestFramework_Request',
                'Magento\Core\Controller\Response\Http' => 'Magento_TestFramework_Response'
            )
        ));
    }

    protected function tearDown()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_objectManager = null;
    }

    /**
     * Ensure that there were no error messages displayed on the admin panel
     */
    protected function assertPostConditions()
    {
        if ($this->_assertSessionErrors) {
            // equalTo() is intentionally used instead of isEmpty() to provide the informative diff
            $this->assertSessionMessages($this->equalTo(array()), \Magento\Core\Model\Message::ERROR);
        }
    }

    /**
     * Run request
     *
     * @param string $uri
     */
    public function dispatch($uri)
    {
        $this->getRequest()->setRequestUri($uri);
        $this->_getBootstrap()->runApp($this->getRequest(), $this->getResponse());
    }

    /**
     * Request getter
     *
     * @return Magento_TestFramework_Request
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = $this->_objectManager->get('Magento_TestFramework_Request');
        }
        return $this->_request;
    }

    /**
     * Response getter
     *
     * @return Magento_TestFramework_Response
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = $this->_objectManager->get('Magento_TestFramework_Response');
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
     * Analyze response object and look for header with specified name, and assert a regex towards its value
     *
     * @param string $headerName
     * @param string $valueRegex
     * @throws PHPUnit_Framework_AssertionFailedError when header not found
     */
    public function assertHeaderPcre($headerName, $valueRegex)
    {
        $headerFound = false;
        $headers = $this->getResponse()->getHeaders();
        foreach ($headers as $header) {
            if ($header['name'] === $headerName) {
                $headerFound = true;
                $this->assertRegExp($valueRegex, $header['value']);
            }
        }
        if (!$headerFound) {
            $this->fail("Header '{$headerName}' was not found. Headers dump:\n" . var_export($headers, 1));
        }
    }

    /**
     * Assert that there is a redirect to expected URL.
     * Omit expected URL to check that redirect to wherever has been occurred.
     * Examples of usage:
     * $this->assertRedirect($this->equalTo($expectedUrl));
     * $this->assertRedirect($this->stringStartsWith($expectedUrlPrefix));
     * $this->assertRedirect($this->stringEndsWith($expectedUrlSuffix));
     * $this->assertRedirect($this->stringContains($expectedUrlSubstring));
     *
     * @param PHPUnit_Framework_Constraint|null $urlConstraint
     */
    public function assertRedirect(PHPUnit_Framework_Constraint $urlConstraint = null)
    {
        $this->assertTrue($this->getResponse()->isRedirect(), 'Redirect was expected, but none was performed.');
        if ($urlConstraint) {
            $actualUrl = '';
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ($header['name'] == 'Location') {
                    $actualUrl = $header['value'];
                    break;
                }
            }
            $this->assertThat($actualUrl, $urlConstraint, 'Redirection URL does not match expectations');
        }
    }

    /**
     * Assert that actual session messages meet expectations:
     * Usage examples:
     * $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::ERROR);
     * $this->assertSessionMessages($this->equalTo(array('Entity has been saved.')),
     * \Magento\Core\Model\Message::SUCCESS);
     *
     * @param PHPUnit_Framework_Constraint $constraint Constraint to compare actual messages against
     * @param string|null $messageType Message type filter, one of the constants \Magento\Core\Model\Message::*
     * @param string $sessionModel Class of the session model that manages messages
     */
    public function assertSessionMessages(
        PHPUnit_Framework_Constraint $constraint, $messageType = null, $sessionModel = 'Magento\Core\Model\Session'
    ) {
        $this->_assertSessionErrors = false;
        /** @var $session \Magento\Core\Model\Session\AbstractSession */
        $session = $this->_objectManager->get($sessionModel);
        $actualMessages = array();
        /** @var $message \Magento\Core\Model\Message\AbstractMessage */
        foreach ($session->getMessages()->getItems($messageType) as $message) {
            $actualMessages[] = $message->getText();
        }
        $this->assertThat($actualMessages, $constraint, 'Session messages do not meet expectations');
    }
}
