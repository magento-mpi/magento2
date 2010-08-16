<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class Test_Abstract extends PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * User name
     *
     * @var string
     */
    protected $_userName = '';

    /**
     * User password
     * 
     * @var string
     */
    protected $_password = '';

    /**
     * Test ID
     * 
     * @var string
     */
    protected $_testId = "";

    /**
     * site name
     *
     * @var string
     */
    protected $_siteName = "";

    /**
     * Site code name
     *
     * @var string
     */
    protected $_siteCode = "";

    /**
     * site sort order
     *
     * @var string
     */
    protected $_siteOrder = "";
    
    /**
     * Root category name
     * 
     * @var string
     */

    protected $_rootCategoryName = "";

    /**
     * Parent subcategory name
     *
     * @var string
     */
    protected $_parentSubCategoryName  = "";

    /**
     * storeView name
     *
     * @var string
     */
    protected $_storeView  = "";


    /**
     * Manage debug function outputs.
     * 0 - silent function
     * 9 - all debug information printed
     * @var int 
     */
    protected $_debugLevel  = "";


    /**
     * Add an error to the stack
     *
     * @param string $error
     */

    function setVerificationErrors($error)
    {
        array_push($this->verificationErrors, $error);
    }

    /**
     * Fetch an Xpath to access a certain UI element
     *
     * @param string $elem
     * @param string | array $arg
     * @return string
     */
    public function getUiElement($elem, $arg = null)
    {
        $element = Core::getEnvMap($elem);
        if ($element==null) {
            $this->debug("No elements found in UIMap for key: ".$elem);
        }
        if (is_array($arg)) {
            return vsprintf($element, $arg);
        } elseif (null !== $arg) {
            $this->debug(sprintf($element, $arg));
            return sprintf($element, $arg);
        } else {
            return $element;
        }
    }

      /**
     * Wait of appearance of html element with Xpath during timeforwait sec
     *
     * @param string $xpath
     * @param int $timeforwait
     * @return boolean
     */
    public function waitForElement($xpath, $timeforwait) {
        for ($second = 0; ; $second++) {
            if ($second >= $timeforwait) {
                $this->fail("element could not be found: " . $xpath);
            }
            try {
                if ($this->isElementPresent($xpath)) {
                    return true;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        return false;
    }

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        $this->setBrowser(Core::getEnvConfig('browser'));
        $this->setBrowserUrl(Core::getEnvConfig('frontend/baseUrl'));
        $this->_testId = strtoupper(get_class($this));
    }


    /**
     * Debug function
     * Puts debug $line to output
     */
    function debug($line, $level=5)
    {
        if ($level>=$this->_debugLevel) {
            echo $line."\n";
        }
    }

}

