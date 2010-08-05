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
        // Should be loaded from some config
        $this->_baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->_userName = Core::getEnvConfig('backend/auth/username');
        $this->_password = Core::getEnvConfig('backend/auth/password');
    }

    /**
     * Performs login into the BackEnd
     *
     */
    public function doLogin($baseurl, $username, $password) {
        $this->open($baseurl);
        $this->waitForPageToLoad("10000");
        $this->type("username", $username);
        $this->type("login", $password);
        $this->click("//input[@title='Login']");
        $this->waitForPageToLoad("90000");
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public  function doPleaseWait()
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

