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
        $this->type("username", $username);
        $this->type("login", $password);
        $this->click("//input[@title='Login']");
        $this->waitForPageToLoad("90000");

        if ($this->isTextPresent($this->getUiElement("admin/messages/invalidlogin"))) {
            $this->setVerificationErrors("Check 1 failed: Invalid login name/passsword");

        }
//        $this->verifyElementPresent($this->getUiElement("admin/images/mainlogo"));
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

