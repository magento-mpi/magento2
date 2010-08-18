<?php
/**
 * Abstract test class for Frontend module
 *
 * @author Magento Inc.
 */
abstract class Test_Frontend_Abstract extends Test_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();

        // Get test parameters
        $this->_baseurl = Core::getEnvConfig('frontend/baseUrl');
        $this->_email = Core::getEnvConfig('frontend/auth/email');
        $this->_password = Core::getEnvConfig('frontend/auth/password');
    }

    /**
     * Login to the FrontEnd
     *
     */
    public function frontLogin($email, $password) {
        $this->open($this->_baseurl);
        $this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
    }

    /**
     * Register customer from FrontEnd
     *
     */
    public function frontRegister($params) {
        $this->open($this->_baseurl);
        $this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
        $this->clickAndWait($this->getUiElement("frontend/pages/login/buttons/register"));
        // Fill register information
        $this->type($this->getUiElement("frontend/pages/register/inputs/firstName"),$params["firstName"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/lastName"),$params["lastName"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/email"),$params["email"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/password"),$params["password"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/confirmation"),$params["password"]);
        //Register customer
        $this->clickAndWait($this->getUiElement("frontend/pages/register/buttons/submit"));
        //Check for some specific validation errors:
        if ($this->isTextPresent($this->getUiElement("frontend/pages/register/messages/alreadyExists"))) {
                $this->setVerificationErrors("frontRegister check 1: customer with such email already registered");
        } else {
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("frontend/pages/register/messages/customerRegistered"),2)) {
                $this->setVerificationErrors("frontRegister check 1: no success message");
            }
        }
    }
}

