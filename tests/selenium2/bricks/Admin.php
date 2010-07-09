<?php

class Helper_Admin extends Helper_Abstract
{
    /**
     * Performs login into the BackEnd
     * 
     */
    public function doLogin($baseurl, $username, $password) {
        $this->_context->open($baseurl);
        $this->_context->waitForPageToLoad("10000");
        $this->_context->type("username", $username);
        $this->_context->type("login", $password);
        $this->_context->click("//input[@title='Login']");
        $this->_context->waitForPageToLoad("90000");
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public  function pleaseWait()
    {
        //
        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  break; //fail("timeout");
            try {
                if (!$this->_context->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->_context->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }
        sleep(1);
    }

}