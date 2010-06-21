<?php

class Helper_Abstract {
    /**
     * TestCase instance
     *
     * @var Test_Abstract
     */
    protected $_context;

    /**
     * UIMap container
     *
     * @var array
     */
    protected  $_uiMap = array();


    /**
     * Constructor
     * Initialize a TestCase context
     */
    public function  __construct() {
        $this->_context = Core::getContext();
    }

    /**
     * Fetch an Xpath to access a certain UI element
     *
     * @param string $elem
     * @return string
     */
    public function getUiElement($elem) {
        return isset($this->_uiMap[$elem]) ? $this->_uiMap[$elem] : null;
    }

    public function waitForElementNsec($xpath, $timeforwait) {
        echo("\nChecking element: ".$xpath);
        $res = false;
        for ($second = 0; ; $second++) {
            if ($second >= $timeforwait) {
                echo(" - Failed! \n");
                break; //$this->_context->fail("timeout");
            }
            try {
                if ($this->_context->isElementPresent($xpath)) {
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