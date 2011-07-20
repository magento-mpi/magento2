<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An extended test case implementation that add usefull helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestCase extends PHPUnit_Extensions_SeleniumTestCase
{

    /**
     * Testcase error
     *
     * @var boolean
     */
    protected $_error = false;

    /**
     * Data helper instance
     *
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper = null;

    /**
     * Data generator helper instance
     *
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * Application helper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Uimap helper instance
     *
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper = null;

    /**
     * Page helper instance
     *
     * @var Mage_Selenium_Helper_Page
     */
    protected $_pageHelper = null;

    /**
     * Error and success messages on page
     *
     * @var array
     */
    public $messages = null;

    /**
     * Current application area
     *
     * @var string
     */
    protected static $_area = 'frontend';

    /**
     * Current page
     *
     * @var string
     */
    protected $_currentPage = '';

    /**
     * Configuration object instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig = null;

    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_paramsHelper = null;

    /**
     * Timeout const
     *
     * @var int
     */
    protected $_browserTimeoutPeriod = 10000;

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $result;

    /**
     * @var    array
     */
    protected $dependencies = array();

    /**
     * Whether or not this test is running in a separate PHP process.
     *
     * @var    boolean
     */
    protected $inIsolation = false;

    /**
     * The name of the test case.
     *
     * @var    string
     */
    protected $name = null;

    /**
     * The name of the expected Exception.
     *
     * @var    mixed
     */
    protected $expectedException = null;

    /**
     * The message of the expected Exception.
     *
     * @var    string
     */
    protected $expectedExceptionMessage = '';

    /**
     * @var    array
     */
    protected $data = array();

    /**
     * @var    array
     */
    protected $dependencyInput = array();

    /**
     * @var array
     */
    protected $_testHelpers = array();

    /*
     * @var string
     */
    protected $_firstPageAfterAdminLogin = 'dashboard';

    /**
     * Success message Xpath
     *
     * @var string
     */
    const xpathSuccessMessage = "//li[normalize-space(@class)='success-msg']/ul/li";

    /**
     * Error message Xpath
     *
     * @var string
     */
    const xpathErrorMessage = "//li[normalize-space(@class)='error-msg']/ul/li
        [not(text()='Bundle with dynamic pricing cannot include custom defined options. Options will not be saved.')]";

    /**
     * Error message Xpath
     *
     * @var string
     */
    const xpathValidationMessage = "//form/descendant::*[normalize-space(@class)='validation-advice' and not(contains(@style,'display: none;'))]";

    /**
     * Field Name xpath with ValidationMessage
     *
     *  @var string
     */
    const xpathFieldNameWithValidationMessage ="/ancestor::*[2]//label/descendant-or-self::*[string-length(text())>1]";

    /**
     * Loading holder XPath
     * @var string
     */
    const xpathLoadingHolder = "//div[@id='loading-mask' and not(contains(@style,'display: none'))]";

    /**
     * Log Out link
     * @var string
     */
    const xpathLogOutAdmin = "//div[@class='header-right']//a[@class='link-logout']";

    /**
     * Admin Logo Xpath
     * @var string
     */
    const xpathAdminLogo = "//img[@class='logo' and contains(@src,'logo.gif')]";

    /**
     * @var string
     */
    const FIELD_TYPE_MULTISELECT = 'multiselect';

    /**
     * @var string
     */
    const FIELD_TYPE_DROPDOWN = 'dropdown';

    /**
     * @var string
     */
    const FIELD_TYPE_CHECKBOX = 'checkbox';

    /**
     * @var string
     */
    const FIELD_TYPE_RADIOBUTTON = 'radiobutton';

    /**
     * @var string
     */
    const FIELD_TYPE_INPUT = 'field';

    /**
     * Constructor
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     * @param  array  $browser
     * @throws InvalidArgumentException
     */
    public function __construct($name = null, array $data = array(), $dataName = '', array $browser = array())
    {
        $this->_testConfig = Mage_Selenium_TestConfiguration::initInstance();
        $this->_dataHelper = $this->_testConfig->getDataHelper();
        $this->_dataGenerator = $this->_testConfig->getDataGenerator();
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $this->_pageHelper = $this->_testConfig->getPageHelper($this, $this->_applicationHelper);
        $this->_uimapHelper = $this->_testConfig->getUimapHelper();

        if ($name !== null) {
            $this->name = $name;
        }
        $this->data = $data;
        $this->dataName = $dataName;

        $this->_browserTimeoutPeriod = $this->_testConfig->getConfigValue('browsers/default/browserTimeoutPeriod');

        parent::__construct($name, $data, $dataName, $browser);
    }

    /**
     * Override to allow load tests helpers
     *
     * @param string $command
     * @param array $arguments
     * @return mixed
     */
    public function __call($command, $arguments)
    {
        if (version_compare(phpversion(), '5.3.0', '<') === true) {
            $helper = false;
            $pos = strpos($command, 'Helper');
            if ($pos !== false) {
                $helper = substr($command, 0, $pos);
            }
        } else {
            $helper = strstr($command, 'Helper', true);
        }

        if ($helper !== false) {
            $helper = $this->_loadHelper($helper);
            if ($helper) {
                return $helper;
            }
        }
        return parent::__call($command, $arguments);
    }

    /**
     * Load tests helper
     *
     * @param string $testScope
     * @param string $helperName
     * @return Mage_Selenium_TestCase
     */
    protected function _loadHelper($testScope, $helperName = 'Helper')
    {
        if (empty($testScope) || empty($helperName)) {
            throw new UnexpectedValueException('Helper name can\'t be empty');
        }

        $helperClassName = $testScope . '_' . $helperName;

        if (!isset($this->_testHelpers[$helperClassName])) {
            if (class_exists($helperClassName)) {
                $this->_testHelpers[$helperClassName] = new $helperClassName;

                if ($this->_testHelpers[$helperClassName] instanceof Mage_Selenium_TestCase) {
                    $this->_testHelpers[$helperClassName]->appendParamsDecorator($this->_paramsHelper);
                }
            } else {
                return false;
            }
        }

        return $this->_testHelpers[$helperClassName];
    }

    /**
     * Implementetion of setUpBeforeClass() in object context
     *
     * @staticvar boolean $_isFirst
     * @return null
     */
    public function setUp()
    {
        static $_isFirst = true;

        if ($_isFirst) {
            $this->setUpBeforeTests();
            $_isFirst = false;
        }
    }

    /**
     * Function is called before all tests in test case
     *
     * @return null
     */
    public function setUpBeforeTests()
    {

    }

    /**
     * Append parameters decorator object
     *
     * @param Mage_Selenium_Helper_Params $paramsHelperObject Parameters decorator object
     */
    public function appendParamsDecorator($paramsHelperObject)
    {
        $this->_paramsHelper = $paramsHelperObject;
    }

    /**
     * Set parameter to decorator object instance
     *
     * @param string $name   Parameter name
     * @param string $value  Parameter value (null to unset)
     * @param Mage_Selenium_Helper_Params $paramsHelperObject Parameters decorator object
     */
    public function addParameter($name, $value)
    {
        if (!$this->_paramsHelper) {
            $this->_paramsHelper = new Mage_Selenium_Helper_Params();
        }
        $this->_paramsHelper->setParameter($name, $value);

        return $this->_paramsHelper;
    }

    /**
     * @param  array $browser
     * @return PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected function getDriver(array $browser)
    {
        $driver = $this->_testConfig->driver;
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);
        // @TODO we need separate driver connections if admin url
        // doesn't start with frontend url
        $driver->setBrowserUrl($this->_applicationHelper->getBaseUrl());
        $driver->start();
        $this->drivers[] = $driver;
        return $driver;
    }

    /**
     * Sets the dependencies of a TestCase.
     *
     * @param  array $dependencies
     * @since  Method available since Release 3.4.0
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Checks if there was error during last operations
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->_error;
    }

    /**
     * Data helper methods
     */

    /**
     * Override Data
     *
     * @param string $value
     * @param string $key
     * @param array $overrideArray
     */
    function overrideData(&$value, $key, $overrideArray)
    {
        foreach ($overrideArray as $overrideField => $fieldValue) {
            if ($overrideField === $key) {
                $value = $fieldValue;
            }
        }
    }

    /**
     * Randomize Data
     *
     * @param string $value
     * @param string $key
     * @param array $randomizeArray
     */
    function randomizeData(&$value, $key, $randomizeArray)
    {
        foreach ($randomizeArray as $randomizeField) {
            if ($randomizeField === $key) {
                $value = $this->generate('string', 5, ':lower:') . '_' . $value;
            }
        }
    }

    /**
     * Get an array of keys from Multidimensional Array
     *
     * @param array $arrayData
     * @param array $arrayKeys
     * @return array
     */
    function arrayKeysRecursion(array $arrayData, &$arrayKeys)
    {
        foreach ($arrayData as $key => $value) {
            if (is_array($value)) {
                $arrayKeys = $this->arrayKeysRecursion($value, $arrayKeys);
            } else {
                $arrayKeys[] = $key;
            }
        }
        return $arrayKeys;
    }

    /**
     * Load test data
     *
     * @param string|array $dataSource Data source (e.g. filename in ../data without .yml extension)
     * @param array|null $override Value to override in original data from data source
     * @param string|array|null $randomize Value to randomize
     * @return array
     */
    public function loadData($dataSource, $override=null, $randomize=null)
    {
        $data = $this->_getData($dataSource);

        if (!empty($override) && is_array($override)) {
//            foreach ($override as $field => $value) {
//                $data[$field] = $value;
//            }
            $arrayKeys = array();
            $needAddValues = array();

            $arrayKeys = $this->arrayKeysRecursion($data, $arrayKeys);

            foreach ($override as $key => $value) {
                if (!in_array($key, $arrayKeys)) {
                    $needAddValues[$key] = $value;
                    unset($override[$key]);
                }
            }

            array_walk_recursive($data, array($this, 'overrideData'), $override);

            foreach ($needAddValues as $field => $value) {
                $data[$field] = $value;
            }
        }

        if (!empty($randomize)) {
            $randomize = (!is_array($randomize)) ? array($randomize) : $randomize;

//            foreach ($randomize as $field) {
//                $data[$field] = $this->generate('string', 5, ':lower:') . '_' . $data[$field];
//            }
            array_walk_recursive($data, array($this, 'randomizeData'), $randomize);
        }

        return $data;
    }

    /**
     * Generates some random value
     *
     * @param string $type Available types are 'string', 'text', 'email'
     * @param int $length Generated value length
     * @param string|array|null $modifier Value modifier, e.g. PCRE class
     * @param string|null $prefix Prefix to prepend the generated value
     * @return mixed
     */
    public function generate($type='string', $length=100, $modifier=null, $prefix=null)
    {
        $result = $this->_dataGenerator->generate($type, $length, $modifier, $prefix);
        return $result;
    }

    /**
     * Navigation methods
     */

    /**
     * Navigate to a specified frontend page
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function frontend($page='home')
    {
        $this->setArea('frontend');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigate to a specified admin page
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function admin($page='dashboard')
    {
        $this->setArea('admin');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigates to a specified page in the current area
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page)
    {
        try {
            $clickXpath = $this->getPageClickXpath($page);

            if ($clickXpath && $this->isElementPresent($clickXpath)) {
                $this->click($clickXpath);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            } else {
                $this->open($this->getPageUrl($page));
            }

            $this->_pageHelper->validateCurrentPage();
            $this->_currentPage = $page;
        } catch (PHPUnit_Framework_Exception $e) {
            $this->_error = true;
        }

        return $this;
    }

    /**
     * Navigates to the specified page in the current area
     * and stops current testcase execution if navigation failed
     *
     * @param string $page Page identifier
     * @return Mage_Selenium_TestCase
     */
    public function navigated($page)
    {
        $this->navigate($page);
        if ($this->_pageHelper->validationFailed()) {
            // @TODO stop further execution of the current test
            $this->_error = true;
        }
        return $this;
    }

    /**
     * Check the current page
     *
     * @param string $page Page identifier
     * @return boolean
     */
    public function checkCurrentPage($page)
    {
        return $this->_findCurrentPageFromUrl($this->getLocation()) == $page;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $page Page identifier
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->_pageHelper->getPageUrl($page);
    }

    /**
     * Return click xpath of a specified page
     *
     * @param string $page Page identifier
     * @return string
     */
    public function getPageClickXpath($page)
    {
        return $this->_pageHelper->getPageClickXpath($page);
    }

    /**
     * Return ID of current page
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Find PageId in UIMap in current area using full page URL
     * @param string Full URL to page
     * @return string|boolean
     */
    protected function _findCurrentPageFromUrl($url)
    {
        $baseUrl = $this->_applicationHelper->getBaseUrl();

        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($baseUrl, $url);
        $page = $this->_pageHelper->getPageByMca($mca, $this->_paramsHelper);
        if ($page) {
            return $page->getPageId();
        } else {
            $this->fail('Can\'t find page for url: ' . $url);
        }

        return false;
    }

    /**
     * Get MCA-part of page URL
     * @param string Base URL
     * @param string Current URL
     * @return string
     */
    protected static function _getMcaFromCurrentUrl($baseUrl, $currentUrl)
    {
        $mca = '';

        $currentUrl = preg_replace('|^http([s]{0,1})://|', '',
                str_replace('/index.php', '/', str_replace('index.php/', '', $currentUrl)));
        $baseUrl = preg_replace('|^http([s]{0,1})://|', '',
                str_replace('/index.php', '/', str_replace('index.php/', '', $baseUrl)));

        if (strpos($currentUrl, $baseUrl) !== false) {
            $mca = trim(substr($currentUrl, strlen($baseUrl)), " /\\");
        }

        if (self::$_area != 'admin') {
            return $mca;
        }

        $mcaArray = explode('/', $mca);

        //Delete secret key from url
        if (in_array('key', $mcaArray)) {
            $key = array_search('key', $mcaArray);
            if ($mcaArray[$key - 1] == 'index') {
                $key = $key - 1;
//                unset($mcaArray[$key - 1]);
            }
//            unset($mcaArray[$key]);
//            unset($mcaArray[$key + 1]);
            $count = count($mcaArray);
            for ($i = $count; $i >= $key; $i--) {
                unset($mcaArray[$i]);
            }
        }

        //Delete action part of mca if it's index
        if (end($mcaArray) == 'index') {
            unset($mcaArray[count($mcaArray) - 1]);
        }

        return implode('/', $mcaArray);
    }

    /**
     * Get current area
     *
     * @return string
     */
    public function getArea()
    {
        return self::$_area;
    }

    /**
     * Set current area
     *
     * @param string $area Area: 'admin' or 'frontend'
     * @return Mage_Selenium_TestCase
     */
    public function setArea($area)
    {
        self::$_area = $area;
        $this->_applicationHelper->setArea($area);
        return $this;
    }

    /**
     * Retrieve Page from uimap data configuration by path
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $pageKey UIMap page key
     * @return Mage_Selenium_Uimap_Page
     */
    public function getUimapPage($area, $pageKey)
    {
        $page = $this->_uimapHelper->getUimapPage($area, $pageKey, $this->_paramsHelper);

        if (!$page) {
            $this->fail('Can\'t find page in area "' . $area . '" for key "' . $pageKey . '"');
        }

        return $page;
    }

    /**
     * Retrieve current Page from uimap data configuration
     *
     * @return Mage_Selenium_Uimap_Page|Null
     */
    public function getCurrentUimapPage()
    {
        return $this->getUimapPage($this->getArea(), $this->_currentPage);
    }

    /**
     * Retrieve Page from uimap data configuration by path
     *
     * @return Mage_Selenium_Uimap_Page|Null
     */
    public function getCurrentLocationUimapPage()
    {
        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($this->_applicationHelper->getBaseUrl(),
                        $this->getLocation());
        $page = $this->_uimapHelper->getUimapPageByMca($this->getArea(), $mca, $this->_paramsHelper);

        if (!$page) {
            $this->fail('Can\'t find page in area "' . $this->getArea() . '" for mca "' . $mca . '"');
        }

        return $page;
    }

    /**
     * Get Xpath of controller
     *
     * @param string $controlType
     * @param string $controlName
     * @return string
     */
    protected function _getControlXpath($controlType, $controlName)
    {
        $uipage = $this->getCurrentLocationUimapPage();
        if (!$uipage) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $method = 'find' . ucfirst(strtolower($controlType));

        $xpath = $uipage->$method($controlName);

        if (is_object($xpath) && method_exists($xpath, 'getXPath')) {
            $xpath = $xpath->getXPath();
        }

        return $xpath;
    }

    /**
     * Click on control
     *
     * @param string $controlType
     * @param string $controlName
     * @param boolean $willChangePage
     * @return Mage_Selenium_TestCase
     */
    public function clickControl($controlType, $controlName, $willChangePage = true)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if (empty($xpath)) {
            $this->fail('Xpath for control "' . $controlName . '" is empty');
        }

        if (!$this->isElementPresent($xpath)) {
            $this->fail('Control "' . $controlName . '" is not present on the page. '
                    . 'Type: ' . $controlType . ', xpath: ' . $xpath);
        }

        try {
            $this->click($xpath);

            if ($willChangePage) {
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }

        return $this;
    }

    /**
     * Click on button
     *
     * @param string $button
     * @param boolean $willChangePage
     * @return Mage_Selenium_TestCase
     */
    public function clickButton($button, $willChangePage = true)
    {
        $this->clickControl('button', $button, $willChangePage);

        return $this;
    }

    /**
     * Search specified control on the page
     *
     * @param string $controlType
     * @param string $controlName
     * @return mixed
     */
    public function controlIsPresent($controlType, $controlName)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if ($xpath == null) {
            $this->fail("Can't find control: [$controlType: $controlName]");
        }

        if ($this->isElementPresent($xpath)) {
            return true;
        }

        return false;
    }

    /**
     * Search specified button on the page
     *
     * @param string $button
     * @return mixed
     */
    public function buttonIsPresent($button)
    {
        return $this->controlIsPresent('button', $button);
    }

    /**
     * Await for appear and disappear "Please wait" animated gif...
     *
     */
    public function pleaseWait($waitAppear = 10, $waitDisappear = 30)
    {
        for ($second = 0; $second < $waitAppear; $second++) {
            if ($this->isElementPresent(Mage_Selenium_TestCase::xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        for ($second = 0; $second < $waitDisappear; $second++) {
            if (!$this->isElementPresent(Mage_Selenium_TestCase::xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        return $this;
    }

    /**
     * Fill form with data
     *
     * @param array|string $data Array with data or datasource name
     * @return Mage_Selenium_TestCase
     */
    public function fillForm($data, $tabId = '')
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $page = $this->getCurrentLocationUimapPage();
        if (!$page) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $formData = $page->getMainForm();

        if (!$formData) {
            return $this;
        }

        $formData->assignParams($this->_paramsHelper);
        if ($tabId && $formData->getTab($tabId)) {
            $fieldsets = $formData->getTab($tabId)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }

        // if we have got empty uimap but not empty dataset
        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        try {
            foreach ($formDataMap as $formFieldName => $formField) {
                switch ($formField['type']) {
                    case self::FIELD_TYPE_INPUT:
                        $this->_fillFormField($formField);
                        break;
                    case self::FIELD_TYPE_CHECKBOX:
                        $this->_fillFormCheckbox($formField);
                        break;
                    case self::FIELD_TYPE_DROPDOWN:
                        $this->_fillFormDropdown($formField);
                        break;
                    case self::FIELD_TYPE_RADIOBUTTON:
                        $this->_fillFormRadiobutton($formField);
                        break;
                    case self::FIELD_TYPE_MULTISELECT:
                        $this->_fillFormMultiselect($formField);
                        break;
                    default:
                        throw new PHPUnit_Framework_Exception('Unsupported field type');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $errorMessage = isset($formFieldName)
                    ? 'Problem with field \'' . $formFieldName . '\': ' . $e->getMessage()
                    : $e->getMessage();
            $this->fail($errorMessage);
        }

        return true;
    }

    /**
     * Map data values to uipage form
     *
     * @param array $fieldsets
     * @param array $data
     * @return array
     */
    protected function _getFormDataMap($fieldsets, $data)
    {
        $dataMap = array();
        $uimapFields = array();

        foreach ($data as $dataFieldName => $dataFieldValue) {
            if ($dataFieldValue == '%noValue%') {
                continue;
            }
            foreach ($fieldsets as $fieldset) {
                $uimapFields[self::FIELD_TYPE_MULTISELECT] = $fieldset->getAllMultiselects();
                $uimapFields[self::FIELD_TYPE_DROPDOWN] = $fieldset->getAllDropdowns();
                $uimapFields[self::FIELD_TYPE_RADIOBUTTON] = $fieldset->getAllRadiobuttons();
                $uimapFields[self::FIELD_TYPE_CHECKBOX] = $fieldset->getAllCheckboxes();
                $uimapFields[self::FIELD_TYPE_INPUT] = $fieldset->getAllFields();
                foreach ($uimapFields as $fieldsType => $fieldsData) {
                    foreach ($fieldsData as $uimapFieldName => $uimapFieldValue) {
                        if ($dataFieldName == $uimapFieldName) {
                            $dataMap[$dataFieldName] = array('type'  => $fieldsType,
                                                             'path'  => $fieldset->getXpath() . $uimapFieldValue,
                                                             'value' => $dataFieldValue);
                            break 3;
                        }
                    }
                }
            }
        }

        return $dataMap;
    }

    /**
     * Fill input form fields
     *
     * @param array $fieldData
     * @return null
     */
    protected function _fillFormField($fieldData)
    {
        if ($this->isElementPresent($fieldData['path']) && $this->isEditable($fieldData['path'])) {
            $this->type($fieldData['path'], $fieldData['value']);
            $this->waitForAjax();
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the field: {$fieldData['path']}");
        }
    }

    /**
     * Fiil multiselect form field
     *
     * @param array $fieldData
     */
    protected function _fillFormMultiselect($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            $this->removeAllSelections($fieldData['path']);
            $valuesArray = explode(',', $fieldData['value']);
            $valuesArray = array_map('trim', $valuesArray);
            foreach ($valuesArray as $value) {
                if ($value != null) {
                    $this->addSelection($fieldData['path'], 'regexp:' . preg_quote($value));
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the multiselect field: {$fieldData['path']}");
        }
    }

    /**
     * Fill form dropdown
     *
     * @param array $fieldData
     */
    protected function _fillFormDropdown($fieldData)
    {
        if ($this->isElementPresent($fieldData['path']) && $this->isEditable($fieldData['path'])) {
            if ($this->isElementPresent($fieldData['path'] . "//option[text()='" . $fieldData['value'] . "']")) {
                $this->select($fieldData['path'], 'label=' . $fieldData['value']);
            } else {
                $this->select($fieldData['path'], 'regexp:' . preg_quote($fieldData['value']));
            }
            $this->waitForAjax();
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the dropdown field: {$fieldData['path']}");
        }
    }

    /**
     * Fill form checkbox field
     *
     * @param array $fieldData
     */
    protected function _fillFormCheckbox($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                if ($this->getValue($fieldData['path']) == 'off') {
                    $this->click($fieldData['path']);
                }
            } else {
                if ($this->getValue($fieldData['path']) == 'on') {
                    $this->click($fieldData['path']);
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the checkbox field: {$fieldData['path']}");
        }
    }

    /**
     * Fill form radiobuttons
     *
     * @param array $fieldData
     */
    protected function _fillFormRadiobutton($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                $this->click($fieldData['path']);
            } else {
                $this->uncheck($fieldData['path']);
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the radiobutton field: {$fieldData['path']}");
        }
    }

    /**
     * Perform search and open first result
     *
     * @param array $data
     * @return Mage_Selenium_TestCase
     */
    public function searchAndOpen(array $data, $willChangePage = true, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);

        if (count($data) > 0) {
            if (isset($fieldSetName)) {
                $xpath = $this->getCurrentLocationUimapPage()->findFieldset($fieldSetName)->getXpath();
            } else {
                $xpath = '';
            }
            //Forming xpath that contains string 'Total $number records found'
            // where $number - number of items in table
            $totalCount = intval($this->getText($xpath
                            . "//table[@class='actions']//td[@class='pager']//span[@id]"));
            $xpathPager = $xpath
                    . "//table[@class='actions']//td[@class='pager']//span[@id and not(text()='" . $totalCount . "')]";

            // Forming xpath for string that contains the lookup data
            $xpathTR = $xpath . "//table[@class='data']//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }

            if (!$this->isElementPresent($xpathTR) && $totalCount > 0) {
                // Fill in search form and click 'Search' button
                $this->fillForm($data);
                $this->clickButton('search', false);
                $this->waitForElement($xpathPager);
            } else if ($totalCount == 0) {
                $this->fail('There is no items in the grid!');
            }

            if ($this->isElementPresent($xpathTR)) {
                if ($willChangePage) {
                    // ID definition
                    $title = $this->getValue($xpathTR . '/@title');
                    if (is_numeric($title)) {
                        $itemId = $title;
                    } else {
                        $titleArr = explode('/', $title);
                        foreach ($titleArr as $key => $value) {
                            if (preg_match('/id$/', $value) and isset($titleArr[$key + 1])) {
                                $itemId = $titleArr[$key + 1];
                                break;
                            }
                        }
                    }
                    $this->addParameter('id', $itemId);
                    $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
                } else {
                    $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                    $this->waitForAjax($this->_browserTimeoutPeriod);
                }
            } else {
                $this->fail('Cant\'t find item in grig for data: ' . print_r($data, true));
            }
        } else {
            $this->fail('Data for search in grid is empty!');
        }

        return true;
    }

    /**
     * Perform search and choose first element
     *
     * @param array $data
     * @return Mage_Selenium_TestCase
     */
    public function searchAndChoose(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);

        if (count($data) > 0) {
            if (isset($fieldSetName)) {
                $xpath = $this->getCurrentLocationUimapPage()->findFieldset($fieldSetName)->getXpath();
            } else {
                $xpath = '';
            }
            //Forming xpath that contains string 'Total $number records found'
            // where $number - number of items in table
            $totalCount = intval($this->getText($xpath
                            . "//table[@class='actions']//td[@class='pager']//span[@id]"));

            // Forming xpath for string that contains the lookup data
            $xpathTR = $xpath . "//table[@class='data']//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }

            if (!$this->isElementPresent($xpathTR) && $totalCount > 0) {
                // Fill in search form and click 'Search' button
                $this->fillForm($data);
                $this->clickButton('search', false);
                $this->pleaseWait();
            } elseif ($totalCount == 0) {
                $this->fail('There is no items in the grid!');
            }

            if ($this->isElementPresent($xpathTR)) {
                $xpathTR .="//input[contains(@class,'checkbox')][not(@disabled)]";
//                $xpathTR .="//input[contains(@class,'checkbox')]";
                if ($this->getValue($xpathTR) == 'off') {
                    $this->click($xpathTR);
                }
            } else {
                $this->fail('Cant\'t find item in grig for data: ' . print_r($data, true));
            }
        } else {
            $this->fail('Data for search in grid is empty!');
        }

        return true;
    }

    /**
     * Prepare data array to grid search
     *
     * @param array $data
     * @return @array
     */
    protected function _prepareDataForSearch(array &$data)
    {
        foreach ($data as $key => $val) {
            if ($val == '%noValue%' or empty($val)) {
                unset($data[$key]);
            } elseif (preg_match('/website/', $key)) {
                $xpathField = $this->getCurrentLocationUimapPage()->getMainForm()->findDropdown($key);
                if (!$this->isElementPresent($xpathField)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Messages helper methods
     */

    /**
     * Add field ID to Message Xpath (set %fieldId% parameter).
     *
     * @param srting $fieldType
     * @param srting $fieldName
     */
    public function addFieldIdToMessage($fieldType, $fieldName)
    {
        $fieldXpath = $this->_getControlXpath($fieldType, $fieldName);
        if ($this->isElementPresent($fieldXpath . '/@id')) {
            $fieldId = $this->getAttribute($fieldXpath . '/@id');
        } else {
            $fieldId = $this->getAttribute($fieldXpath . '/@name');
        }
        $this->addParameter('fieldId', $fieldId);
    }

    /**
     * Check if message exists on page
     *
     * @param string $message  Message Id from UIMap
     * @return boolean
     */
    public function checkMessage($message)
    {
        $page = $this->getCurrentLocationUimapPage();
        $messageLocator = $page->findMessage($message);
        return $this->checkMessageByXpath($messageLocator);
    }

    /**
     * Check if message with given xpath exists on page
     *
     * @param string $xpath
     * @return boolean
     */
    public function checkMessageByXpath($xpath)
    {
        $this->_parseMessages();
        if ($xpath && $this->getXpathCount($xpath) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if any error message exists on page
     *
     * @return boolean
     */
    public function errorMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathErrorMessage);
    }

    /**
     * Return all error messages on page
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $this->_parseMessages();

        return $this->messages['error'];
    }

    /**
     * Check if any success message exists on page
     *
     * @return boolean
     */
    public function successMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathSuccessMessage);
    }

    /**
     * Return all success messages on page
     *
     * @return array
     */
    public function getSuccessMessages()
    {
        $this->_parseMessages();

        return $this->messages['success'];
    }

    /**
     * Check if any validation message exists on page
     *
     * @return boolean
     */
    public function validationMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathValidationMessage);
    }

    /**
     * Return all error messages on page
     *
     * @return array
     */
    public function getValidationMessages()
    {
        $this->_parseMessages();

        return $this->messages['validation'];
    }

    /**
     * Get all messages on page
     */
    protected function _parseMessages()
    {
        $this->messages['success'] = $this->getElementsByXpath(self::xpathSuccessMessage);
        $this->messages['error'] = $this->getElementsByXpath(self::xpathErrorMessage);
        $this->messages['validation'] = $this->getElementsByXpath(self::xpathValidationMessage,
                        'text', self::xpathFieldNameWithValidationMessage);
    }

    /**
     * Get elements by Xpath
     *
     * @param string $xpath
     * @param string $get    What to get. Available choices are 'text', 'value'
     * @return array
     */
    public function getElementsByXpath($xpath, $get = 'text', $additionalXPath = '')
    {
        $elements = array();

        if (!empty($xpath)) {
            if ('/' !== substr($xpath, 0, 1)) {
                $xpath = $xpath;
            }

            $totalElements = $this->getXpathCount($xpath);

            for ($i = 1; $i < $totalElements + 1; $i++) {
                $x = $xpath . '[' . $i . ']';

                switch ($get) {
                    case 'value' :
                        $element = $this->getValue($x);
                        break;
                    case 'text' :
                    default :
                        $element = $this->getText($x);
                        break;
                }

                if (!empty($element)) {
                    if ($additionalXPath) {
                        if ($this->isElementPresent($x . $additionalXPath)) {
                            $label = trim($this->getText($x . $additionalXPath), " *\t\n\r");
                        } else {
                            $label = $this->getAttribute($x . "@id");
                            $label = strrev($label);
                            $label = strrev(substr($label, 0, strpos($label, "-")));
                        }
                        if ($label) {
                            $element = "'" . $label . "': " . $element;
                        }
                    }

                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Get element by Xpath
     *
     * @param string $xpath
     * @param string $get    What to get. Available choices are 'text', 'value'
     * @return array
     */
    public function getElementByXpath($xpath, $get = 'text')
    {
        return array_shift($this->getElementsByXpath($xpath, $get));
    }

    /**
     * Magento helper methods
     */

    /**
     * Log out customer
     *
     * @return Mage_Selenium_TestCase
     */
    public function logoutCustomer()
    {
        try {
            $this->frontend('customer_account');
            if ("My Account" == $this->getTitle()) {
                $this->clickAndWait("//a[@title='Log Out']", $this->_browserTimeoutPeriod);
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->_error = true;
        }
        return $this;
    }

    /**
     * Log in admin
     *
     * @return Mage_Selenium_TestCase
     */
    public function loginAdminUser()
    {
        try {
            $this->admin('log_in_to_admin');

            if (!$this->checkCurrentPage($this->_firstPageAfterAdminLogin)) {
                if ($this->checkCurrentPage('log_in_to_admin')) {
                    $loginData = array('user_name' => $this->_applicationHelper->getDefaultAdminUsername(),
                                       'password'  => $this->_applicationHelper->getDefaultAdminPassword());
                    $this->fillForm($loginData);
                    $this->clickButton('login', false);
                    $this->waitForElement(array(self::xpathAdminLogo,
                                                self::xpathErrorMessage,
                                                self::xpathValidationMessage));
                    if (!$this->checkCurrentPage($this->_firstPageAfterAdminLogin)) {
                        throw new PHPUnit_Framework_Exception('Admin was not logged in');
                    }
                    $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
                } else {
                    throw new PHPUnit_Framework_Exception('Wrong page was opened');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Log out admin user
     *
     * @return Mage_Selenium_TestCase
     */
    public function logoutAdminUser()
    {
        try {
            if ($this->isElementPresent(self::xpathLogOutAdmin)) {
                $this->click(self::xpathLogOutAdmin);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
                if (!$this->checkCurrentPage('log_in_to_admin')) {
                    throw new PHPUnit_Framework_Exception('Admin was not logged out');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Asserts that a condition is true.
     *
     * @param  boolean $condition
     * @param  string  $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        if (is_array($message)) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isTrue(), $message);

        if (isset($this)) {
            return $this;
        }
    }

    /**
     * Asserts that a condition is false.
     *
     * @param  boolean $condition
     * @param  string  $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        if (is_array($message)) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isFalse(), $message);

        if (isset($this)) {
            return $this;
        }
    }

    /**
     * Get node from data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    protected function _getData($path='')
    {
        return $this->_testConfig->getDataValue($path);
    }

    /**
     * Delete opened element
     *
     * @param string $buttonName
     * @param string $message
     */
    public function deleteElement($buttonName, $message)
    {
        $buttonXpath = $this->_getControlXpath('button', $buttonName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->getCurrentLocationUimapPage()->findMessage($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->getConfirmation();
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

                    return true;
                } else {
                    $this->messages['error'][] = "The confirmation text incorrect: {$text}\n";
                }
            } else {
                $this->messages['error'][] = "The confirmation does not appear\n";
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

                return true;
            }
        } else {
            $this->messages['error'][] = "There is no way to remove an item(There is no 'Delete' button)\n";
        }

        return false;
    }

    /**
     * Waiting for element appearance
     *
     * @param string|array $locator xPath locator or array of locators
     * @param integer $timeout Timeout period
     */
    public function waitForElement($locator, $timeout = 30)
    {
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {

            if (is_array($locator)) {
                foreach ($locator as $loc) {
                    if ($this->isElementPresent($loc)) {
                        return true;
                    }
                }
            } else {
                if ($this->isElementPresent($locator)) {
                    return true;
                }
            }
            sleep(1);
        }
        return false;
    }

    /**
     * Waiting for AJAX request to continue
     * NOTE: Method works only if AJAX request was perform
     *       with Prototype or JQuery framework
     *
     * @param integer $timeout Timeout period
     */
    public function waitForAjax($timeout = 30000)
    {
        $jsCondition = 'var c = function(){if(typeof selenium.browserbot.getCurrentWindow().Ajax != "undefined"){'
                . 'if(selenium.browserbot.getCurrentWindow().Ajax.activeRequestCount){return false;};};'
                . 'if(typeof selenium.browserbot.getCurrentWindow().jQuery != "undefined"){'
                . 'if(selenium.browserbot.getCurrentWindow().jQuery.active){return false;};};return true;};c();';
        $this->waitForCondition($jsCondition, $timeout);
    }

    /**
     * Save standart form
     *
     * @param string $buttonName
     */
    public function saveForm($buttonName)
    {
        $this->clickButton($buttonName, false);
        $this->waitForElement(array(self::xpathErrorMessage,
                                    self::xpathValidationMessage,
                                    self::xpathSuccessMessage));
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

        return $this;
    }

    /**
     * Verify form values
     *
     * @param array|string $data Array with data or datasource name
     * @param string $tabName
     */
    public function verifyForm($data, $tabName = '', $skipElements = array('password'))
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $page = $this->getCurrentLocationUimapPage();
        if (!$page) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $formData = $page->getMainForm();
        if (!$formData) {
            throw new OutOfRangeException("Can't find main form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }
        $formData->assignParams($this->_paramsHelper);

        if ($tabName) {
            $fieldsets = $formData->getTab($tabName)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }

        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        $resultFlag = true;
        foreach ($data as $d_key => $d_val) {

            if (in_array($d_key, $skipElements) || $d_val == '%noValue%') {
                continue;
            }

            foreach ($fieldsets as $fieldsetName => $fieldset) {
                // Next fieldset flag
                $stopFlag = false;

                $baseXpath = $fieldset->getXPath();

                // ----------------------------------------------------
                $fields = $fieldset->getAllMultiselects();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $labels = $this->getSelectedLabels($elemXPath);
                                if (!in_array($d_val, $labels)) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllDropdowns();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $labels = $this->getSelectedLabels($elemXPath);
                                if (!in_array($d_val, $labels)) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllRadiobuttons();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $f_val = $this->getValue($elemXPath);
                                if (($f_val == 'on' && strtolower($d_val) != 'yes') ||
                                        ($f_val == 'off' && !(strtolower($d_val) == 'no' || $d_val == ''))) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllCheckboxes();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $f_val = $this->getValue($elemXPath);
                                if (($f_val == 'on' && strtolower($d_val) != 'yes') ||
                                        ($f_val == 'off' && !(strtolower($d_val) == 'no' || $d_val == ''))) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllFields();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {
                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                if ($this->getValue($elemXPath) != $d_val) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }
            }
        }

        return $resultFlag;
    }

    /**
     * Verify messages count
     *
     * @param integer $count
     * @param string $xpath
     */
    public function verifyMessagesCount($count = 1, $xpath = Mage_Selenium_TestCase::xpathValidationMessage)
    {
        if (!preg_match('/^\/\//', $xpath)) {
            $xpath = '//' . $xpath;
        }
        return $this->getXpathCount($xpath) == $count;
    }

    /**
     * Verify element present
     *
     * @param <type> $xpath
     */
    public function verifyElementPresent($xpath)
    {
        try {
            $this->assertTrue($this->isElementPresent($xpath));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->verificationErrors[] = $e->toString();
        }
    }

    /**
     * redefined PHPUnit_Extensions_SeleniumTestCase::suite
     * make possible to use dependency
     *
     * @param  string $className
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite($className)
    {

        $suite = new PHPUnit_Framework_TestSuite;
        $suite->setName($className);

        $class = new ReflectionClass($className);
        $classGroups = PHPUnit_Util_Test::getGroups($className);
        $staticProperties = $class->getStaticProperties();

        // Create tests from Selenese/HTML files.
        if (isset($staticProperties['seleneseDirectory']) &&
                is_dir($staticProperties['seleneseDirectory'])) {
            $files = array_merge(
                    self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.htm'),
                    self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.html')
            );

            // Create tests from Selenese/HTML files for multiple browsers.
            if (!empty($staticProperties['browsers'])) {
                foreach ($staticProperties['browsers'] as $browser) {
                    $browserSuite = new PHPUnit_Framework_TestSuite;
                    $browserSuite->setName($className . ': ' . $browser['name']);

                    foreach ($files as $file) {
                        $browserSuite->addTest(
                                //new $className($file, array(), '', $browser),
                                self::addTestDependencies(
                                        new $className($file, array(), '', $browser), $className,
                                        $name), $classGroups
                        );
                    }

                    $suite->addTest($browserSuite);
                }
            }

            // Create tests from Selenese/HTML files for single browser.
            else {
                foreach ($files as $file) {
                    $suite->addTest(new $className($file), $classGroups);
                }
            }
        }

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties['browsers'])) {
            foreach ($staticProperties['browsers'] as $browser) {
                $browserSuite = new PHPUnit_Framework_TestSuite;
                $browserSuite->setName($className . ': ' . $browser['name']);

                foreach ($class->getMethods() as $method) {
                    if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                        $name = $method->getName();
                        $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                        $groups = PHPUnit_Util_Test::getGroups($className, $name);

                        // Test method with @dataProvider.
                        if (is_array($data) || $data instanceof Iterator) {
                            $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                                            $className . '::' . $name
                            );

                            foreach ($data as $_dataName => $_data) {
                                $dataSuite->addTest(
                                        //new $className($name, $_data, $_dataName, $browser),
                                        self::addTestDependencies(
                                                new $className($name, $_data, $_dataName, $browser),
                                                $className, $name), $groups
                                );
                            }

                            $browserSuite->addTest($dataSuite);
                        }

                        // Test method with invalid @dataProvider.
                        else if ($data === false) {
                            $browserSuite->addTest(
                                    new PHPUnit_Framework_Warning(
                                            sprintf(
                                                    'The data provider specified for %s::%s is invalid.',
                                                    $className, $name
                                            )
                                    )
                            );
                        }

                        // Test method without @dataProvider.
                        else {
                            $browserSuite->addTest(
                                    // new $className($name, array(), '', $browser),
                                    self::addTestDependencies(
                                            new $className($name, array(), '', $browser),
                                            $className, $name), $groups
                            );
                        }
                    }
                }

                $suite->addTest($browserSuite);
            }
        }

        // Create tests from test methods for single browser.
        else {
            foreach ($class->getMethods() as $method) {
                if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                    $name = $method->getName();
                    $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                    $groups = PHPUnit_Util_Test::getGroups($className, $name);

                    // Test method with @dataProvider.
                    if (is_array($data) || $data instanceof Iterator) {
                        $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                                        $className . '::' . $name
                        );

                        foreach ($data as $_dataName => $_data) {
                            $dataSuite->addTest(
                                    //new $className($name, $_data, $_dataName),
                                    self::addTestDependencies(
                                            new $className($name, $_data, $_dataName), $className,
                                            $name), $groups
                            );
                        }

                        $suite->addTest($dataSuite);
                    }

                    // Test method with invalid @dataProvider.
                    else if ($data === false) {
                        $suite->addTest(
                                new PHPUnit_Framework_Warning(
                                        sprintf(
                                                'The data provider specified for %s::%s is invalid.',
                                                $className, $name
                                        )
                                )
                        );
                    }

                    // Test method without @dataProvider.
                    else {
                        $suite->addTest(
                                // new $className($name),
                                self::addTestDependencies(new $className($name), $className, $name),
                                $groups
                        );
                    }
                }
            }
        }

        return $suite;
    }

    /**
     * takes a test and adds its dependencies
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  string $className
     * @param  string $methodName
     * @return void
     */
    public static function addTestDependencies(PHPUnit_Framework_Test $test, $className, $methodName)
    {
        if ($test instanceof PHPUnit_Framework_TestCase ||
                $test instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            $test->setDependencies(
                    PHPUnit_Util_Test::getDependencies($className, $methodName)
            );
        }
        return $test;
    }

    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }

        //$this->setResult($result);
        $this->result = $result;
        $this->setExpectedExceptionFromAnnotation();
        $this->setUseErrorHandlerFromAnnotation();
        $this->setUseOutputBufferingFromAnnotation();

        $this->collectCodeCoverageInformation = $result->getCollectCodeCoverageInformation();

        foreach ($this->drivers as $driver) {
            $driver->setCollectCodeCoverageInformation(
                    $this->collectCodeCoverageInformation
            );
        }

        if (!$this->handleDependencies()) {
            return;
        }

        $result->run($this);

        if ($this->collectCodeCoverageInformation) {
            $result->getCodeCoverage()->append(
                    $this->getCodeCoverage(), $this
            );
        }

        return $result;
    }

    /**
     * @since Method available since Release 3.5.4
     */
    protected function handleDependencies()
    {
        if (!empty($this->dependencies) && !$this->inIsolation) {
            $className = get_class($this);
            $passed = $this->result->passed();

            $passedKeys = array_keys($passed);
            $numKeys = count($passedKeys);

            for ($i = 0; $i < $numKeys; $i++) {
                $pos = strpos($passedKeys[$i], ' with data set');

                if ($pos !== false) {
                    $passedKeys[$i] = substr($passedKeys[$i], 0, $pos);
                }
            }

            $passedKeys = array_flip(array_unique($passedKeys));

            foreach ($this->dependencies as $dependency) {
                if (strpos($dependency, '::') === false) {
                    $dependency = $className . '::' . $dependency;
                }

                if (!isset($passedKeys[$dependency])) {
                    $this->result->addError(
                            $this,
                            new PHPUnit_Framework_SkippedTestError(
                                    sprintf('This test depends on "%s" to pass.', $dependency)
                            ), 0
                    );

                    return false;
                } else {
                    if (isset($passed[$dependency])) {
                        $this->dependencyInput[] = $passed[$dependency];
                    } else {
                        $this->dependencyInput[] = null;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Override to run the test and assert its state.
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function runTest()
    {
        if ($this->name === null) {
            throw new PHPUnit_Framework_Exception(
                    'PHPUnit_Framework_TestCase::$name must not be null.'
            );
        }

        try {
            $class = new ReflectionClass($this);
            $method = $class->getMethod($this->name);
        } catch (ReflectionException $e) {
            $this->fail($e->getMessage());
        }

        try {

            $testResult = $method->invokeArgs(
                            $this, array_merge($this->data, $this->dependencyInput)
            );
        } catch (Exception $e) {
            if (!$e instanceof PHPUnit_Framework_IncompleteTest &&
                    !$e instanceof PHPUnit_Framework_SkippedTest &&
                    is_string($this->expectedException) &&
                    $e instanceof $this->expectedException) {
                if (is_string($this->expectedExceptionMessage) &&
                        !empty($this->expectedExceptionMessage)) {
                    $this->assertContains(
                            $this->expectedExceptionMessage, $e->getMessage()
                    );
                }

                if (is_int($this->expectedExceptionCode) &&
                        $this->expectedExceptionCode !== 0) {
                    $this->assertEquals(
                            $this->expectedExceptionCode, $e->getCode()
                    );
                }

                $this->numAssertions++;

                return;
            } else {
                throw $e;
            }
        }

        if ($this->expectedException !== null) {
            $this->numAssertions++;

            $this->syntheticFail(
                    'Expected exception ' . $this->expectedException, '', 0,
                    $this->expectedExceptionTrace
            );
        }

        return $testResult;
    }

}
