<?php

class AdminHelper {
    protected $_object;
    public function  __construct($object = null) {
        $this->_object = $object;
    }
    public function doLogin($baseurl, $username, $password) {
        $this->_object->open($baseurl);
        $this->_object->waitForPageToLoad("10000");
        $this->_object->type("username", $username);
        $this->_object->type("login", $password);
        $this->_object->click("//input[@title='Login']");
        $this->_object->waitForPageToLoad("90000");

    }

    public  function pleaseWait() {
        //
        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  break; //fail("timeout");
            try {
                if (!$this->_object->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->_object->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }
    }

    public function waitForElementNsec($xpath, $timeforwait) {
        echo("\nChecking element: ".$xpath);
        $res = false;
        for ($second = 0; ; $second++) {
            if ($second >= $timeforwait) {
                echo(" - Failed! \n");
                break; //$this->_object->fail("timeout");
            }
            try {
                if ($this->_object->isElementPresent($xpath)) {
                    $res=true;
                    echo(" - Matched on ".$second." sec.\n");
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        echo ("result=".$res."\n");
        return $res;
    }


}