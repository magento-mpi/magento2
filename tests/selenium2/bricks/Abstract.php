<?php

class Helper_Abstract {
    /**
     * TestCase instance
     *
     * @var Test_Abstract
     */
    protected $_context;

    /**
     * Helper instance for Singleton implementation
     *
     * @var Helper_Abstract
     */
    protected static $_instance = null;

    /**
     * UIMap container
     *
     * @var array
     */
    protected  $_uiMap = array();


    /**
     * Constructor
     * Initialize a TestCase context
     * Is protected for Singleton implementation
     */
    protected function  __construct() {
        $this->_context = Core::getContext();
    }

    /**
     * Fetch the helper instance
     *
     * @return Helper_Abstract
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
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