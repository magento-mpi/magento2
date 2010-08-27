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
     * site store name
     *
     * @var string
     */
    protected $_storeName = "";

    /**
     * site storeview name
     *
     * @var string
     */
    protected $_storeviewName= "";

    /**
     * storeview code
     *
     * @var string
     */
    protected $_storeviewCode = "";

    /**
     * _storeviewStatus
     *
     * @var string
     */
    protected $_storeviewStatus = "";

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
     * Manage debug function outputs.
     * 0 - silent function
     * 9 - all debug information printed
     * @var int 
     */
    protected $_debugLevel  = 7;

    /**
     * Attribute Set name
     *
     * @var string
     */
    protected $_attributeSetName  = "";

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
            Core::debug("No elements found in UIMap for key: ".$elem);
        }
        if (is_array($arg)) {
            Core::debug("getUiElement(...): ".vsprintf($element, $arg),7);
            return vsprintf($element, $arg);
        } elseif (null !== $arg) {
            Core::debug("getUiElement(...): ".sprintf($element, $arg),7);
            return sprintf($element, $arg);
        } else {
            Core::debug("getUiElement(...): ".$element,7);
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
    public function waitForElement($xpath, $timeforwait = 30) {
        Core::debug("waitForElement: waiting for ".$xpath,7);
        for ($second = 0; ; $second++) {
            if ($second >= $timeforwait) {
                 //$this->fail("element could not be found: " . $xpath);
                  Core::debug('waitForElement failed', 7);
                  return false;
            }
            try {
                if ($this->isElementPresent($xpath)) {
                    Core::debug('waitForElement passed', 7);
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
        $this-> setTimeout(60*1000);
    }

    /**
     * Select $countryName in countries dropdown
     * @param $tableBaseURL - xpath for table with address fields
     * @param $countryName - country name
     * @return boolean
     */
    public function selectCountry($selectorID, $countryName)
    {
        $paramsArray = array (
            "$selectorID" => $selectorID,
            "$countryName"  => $countryName
        );
        Core::debug($this->getUiElement('elements/selectedCountry',$paramsArray));
        if (!$this->isElementPresent($this->getUiElement('elements/selectedCountry',$paramsArray))) {
            $this->select($selectorID, $countryName);
//            Required  for Admin !
//            $this->pleaseWait();
            return true;
        } else {
            $this->select($selectorID, $countryName);
           return false;
        }
    }

   /**
     * Select $regionName in region dropdown
     * @param $selectorID - xpath for element
     * @param $regionName - region name
     * @return boolean
     */
    public function selectRegion($selectorID, $regionName)
    {
        $paramsArray = array (
            "$selectorID" => $selectorID,
            "$regionName" => $regionName
        );
        if ($this->isElementPresent("//select[@id='" . $selectorID . "' and contains(@style,'display: none')]")) {
            // region selector is input
            Core::debug("region field is input\n".$selectorID."\n",5);
            $this->type("billing:region",$regionName);
            return true;
        } else {
            // region selector have "dropdown" type
            Core::debug("region field is dropdown",5);
            if (!$this->isElementPresent($this->getUiElement('elements/selectedRegion',$paramsArray))) {
                $this->select($selectorID, $regionName);
             return false;
            }
        }
    }

    public function findAddressByMask($selector, $mask)
    {
            core::debug('findAddressByMask() started',7);
            $addressCount = $this->getXpathCount($selector.'/option');
            core::debug($addressCount . ' addresses founded',7);

            for ($i=1;$i<=$addressCount;$i++) {
                core::debug($i . ' address: ', 7);
                $xpath = $selector . "/option[".$i."]";
                core::debug('PROBING: ' . $xpath, 7);
                $addressValue = $this->getText($xpath);
                core::debug('result: ' . $addressValue, 7);
                if (preg_match($mask, $addressValue)) {
                    core::debug('matched: ' . $xpath, 7);
                    return $i;
                }
            };
            core::debug('NOT matched', 7);
            return -1;
    }
}

