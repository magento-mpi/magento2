<?php
/**
 * Abstract test class for Admin module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Abstract extends Test_Abstract
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
        $this->_baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->_userName = Core::getEnvConfig('backend/auth/username');
        $this->_password = Core::getEnvConfig('backend/auth/password');
    }

    /**
     * Performs login into the BackEnd
     *
     */
    public function adminLogin($baseurl, $username, $password) {
        $this->open($baseurl);
        $this->type($this->getUiElement("admin/pages/login/fields/username"), $username);
        $this->type($this->getUiElement("admin/pages/login/fields/password"), $password);
        $this->clickAndWait($this->getUiElement("admin/pages/login/buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("admin/pages/login/messages/invalidlogin"))) {
            $this->setVerificationErrors("Check 1 failed: Invalid login name/passsword");

        }
        if ($this->isElementPresent($this->getUiElement("admin/pages/login/images/mainlogo"))) {
            $this->setVerificationErrors("Check 1 failed: Dashboard wasn't loaded");
    }
    }
    /**
     * Await appearing and disappearing "Please wait" gif-image
     *
     */
    
    public  function pleaseWait()
    {
        //
        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  {
                break; //fail("timeout");
            }
            try {
                if (!$this->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        sleep(1);
    }

}

