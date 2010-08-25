<?php
/**
 * Admin framework model
 *
 * @author Magento Inc.
 */
class Model_Admin extends TestModelAbstract
{

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->setBrowserUrl($this->baseUrl);
        $this->userName = Core::getEnvConfig('backend/auth/username');
        $this->password = Core::getEnvConfig('backend/auth/password');
    }

    /**
     * Performs login into the BackEnd
     *
     */
    public function doLogin($userName = null, $password = null) {
        $userName = $userName ? $userName : $this->userName;
        $password = $password ? $password : $this->password;

        $this->open($this->baseUrl);
        $this->waitForPageToLoad("10000");

        $this->setUiNamespace('admin/pages/login');

        $this->type($this->getUiElement("fields/username"), $userName);
        $this->type($this->getUiElement("fields/password"), $password);
        $this->clickAndWait($this->getUiElement("buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("messages/invalidlogin"))) {
            $this->setVerificationErrors("Login check 1 failed: Invalid login name/passsword");
        }
        if (!$this->waitForElement($this->getUiElement("images/mainlogo"), 30)) {
            $this->setVerificationErrors("Check 1 failed: Dashboard hasn't loaded");
        }
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public  function pleaseWait()
    {
        $loadingMask = $this->getUiElement('admin/progressBar');

        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  {
                break; //fail("timeout");
            }
            try {
                if (!$this->isElementPresent($loadingMask)) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->isElementPresent($loadingMask)) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        sleep(1);
    }
}

