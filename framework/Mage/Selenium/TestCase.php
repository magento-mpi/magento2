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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An extended test case implementation that adds useful helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Core_Mage_AdminUser_Helper adminUserHelper()
 * @method Core_Mage_AttributeSet_Helper attributeSetHelper()
 * @method Core_Mage_Category_Helper categoryHelper()
 * @method Core_Mage_CheckoutMultipleAddresses_Helper|Enterprise_Mage_CheckoutMultipleAddresses_Helper checkoutMultipleAddressesHelper()
 * @method Core_Mage_CheckoutOnePage_Helper|Enterprise_Mage_CheckoutOnePage_Helper checkoutOnePageHelper()
 * @method Core_Mage_CmsPages_Helper cmsPagesHelper()
 * @method Core_Mage_CmsPolls_Helper cmsPollsHelper()
 * @method Core_Mage_CmsStaticBlocks_Helper cmsStaticBlocksHelper()
 * @method Core_Mage_CmsWidgets_Helper|Enterprise_Mage_CmsWidgets_Helper cmsWidgetsHelper()
 * @method Core_Mage_CompareProducts_Helper compareProductsHelper()
 * @method Core_Mage_CustomerGroups_Helper customerGroupsHelper()
 * @method Core_Mage_Customer_Helper customerHelper()
 * @method Core_Mage_Installation_Helper installationHelper()
 * @method Core_Mage_Newsletter_Helper newsletterHelper()
 * @method Core_Mage_OrderCreditMemo_Helper orderCreditMemoHelper()
 * @method Core_Mage_OrderInvoice_Helper orderInvoiceHelper()
 * @method Core_Mage_OrderShipment_Helper orderShipmentHelper()
 * @method Core_Mage_Order_Helper|Enterprise_Mage_Order_Helper orderHelper()
 * @method Core_Mage_Paypal_Helper paypalHelper()
 * @method Core_Mage_PriceRules_Helper priceRulesHelper()
 * @method Core_Mage_ProductAttribute_Helper productAttributeHelper()
 * @method Core_Mage_Product_Helper|Enterprise_Mage_Product_Helper productHelper()
 * @method Core_Mage_Rating_Helper ratingHelper()
 * @method Core_Mage_Review_Helper reviewHelper()
 * @method Core_Mage_ShoppingCart_Helper|Enterprise_Mage_ShoppingCart_Helper shoppingCartHelper()
 * @method Core_Mage_Store_Helper storeHelper()
 * @method Core_Mage_SystemConfiguration_Helper systemConfigurationHelper()
 * @method Core_Mage_Tags_Helper tagsHelper()
 * @method Core_Mage_Tax_Helper taxHelper()
 * @method Core_Mage_Wishlist_Helper|Enterprise_Mage_Wishlist_Helper wishlistHelper()
 * @method Enterprise_Mage_StagingWebsite_Helper stagingWebsiteHelper()
 * @method Enterprise_Mage_GiftWrapping_Helper giftWrappingHelper()
 * @method Enterprise_Mage_Rollback_Helper rollbackHelper()
 */
class Mage_Selenium_TestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    ################################################################################
    #              Framework variables and constant                                #
    ################################################################################
    /**
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig;

    /**
     * Config helper instance
     * @var Mage_Selenium_Helper_Config
     */
    protected $_configHelper;

    /**
     * UIMap helper instance
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper;

    /**
     * Data helper instance
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper;

    /**
     * Params helper instance
     * @var Mage_Selenium_Helper_Params
     */
    protected $_paramsHelper;

    /**
     * Data Generator helper instance
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGeneratorHelper;

    /**
     * Array of Test Helper instances
     * @var array
     */
    protected static $_testHelpers = array();

    /**
     * Framework setting
     * @var array
     */
    public $frameworkConfig;

    /**
     * Saves HTML content of the current page if the test failed
     * @var bool
     */
    protected $_saveHtmlPageOnFailure = false;

    /**
     * @var bool
     */
    protected $_saveScreenshotOnFailure = false;

    /**
     * @var null
     */
    protected $_screenshotPath = null;

    /**
     * Timeout in seconds
     * @var int
     */
    protected $_browserTimeoutPeriod = 60;

    /**
     * Name of the first page after logging into the back-end
     * @var string
     */
    protected $_firstPageAfterAdminLogin = 'dashboard';

    /**
     * Array of messages on page
     * @var array
     */
    protected static $_messages = array();

    /**
     * Name of run Test Class
     * @var null
     */
    public static $_testClass = null;

    /**
     * Name of last testcase in test class
     * @var array
     */
    protected static $_lastTestNameInClass = null;

    /**
     * Additional params for navigation URL
     * @var string
     */
    private $_urlPostfix;

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_MULTISELECT = 'multiselect';

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_DROPDOWN = 'dropdown';

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_CHECKBOX = 'checkbox';

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_RADIOBUTTON = 'radiobutton';

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_INPUT = 'field';

    /**
     * Type of uimap elements
     * @var string
     */
    const FIELD_TYPE_PAGEELEMENT = 'pageelement';

    ################################################################################
    #                             Else variables                                   #
    ################################################################################
    /**
     * Loading holder XPath
     * @staticvar string
     */
    protected static $xpathLoadingHolder = "//div[@id='loading-mask'][not(contains(@style,'display:') and contains(@style,'none'))]";

    /**
     * Constructs a test case with the given name and browser to test execution
     *
     * @param  string $name Test case name(by default = null)
     * @param  array  $data Test case data array(by default = array())
     * @param  string $dataName Name of Data set(by default = '')
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->_testConfig = Mage_Selenium_TestConfiguration::getInstance();
        $this->_configHelper = $this->_testConfig->getHelper('config');
        $this->_uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->_dataHelper = $this->_testConfig->getHelper('data');
        $this->_paramsHelper = $this->_testConfig->getHelper('params');
        $this->_dataGeneratorHelper = $this->_testConfig->getHelper('dataGenerator');
        $this->frameworkConfig = $this->_configHelper->getConfigFramework();

        parent::__construct($name, $data, $dataName);

        $this->_screenshotPath = $this->getDefaultScreenshotPath();
        $this->_saveScreenshotOnFailure = $this->frameworkConfig['saveScreenshotOnFailure'];
        $this->_saveHtmlPageOnFailure = $this->frameworkConfig['saveHtmlPageOnFailure'];
    }

    /**
     * Delegate method calls to the driver. Overridden to load test helpers
     *
     * @param string $command Command (method) name to call
     * @param array $arguments Arguments to be sent to the called command (method)
     *
     * @return mixed
     */
    public function __call($command, $arguments)
    {
        $helper = substr($command, 0, strpos($command, 'Helper'));
        if ($helper) {
            $helper = $this->_loadHelper($helper);
            if ($helper) {
                return $helper;
            }
        }
        return parent::__call($command, $arguments);
    }

    /**
     * Implementation of setUpBeforeClass() method in the object context, called as setUpBeforeTests()<br>
     * Used ONLY one time before execution of each class (tests in test class)
     * @throws Exception
     */
    private function setUpBeforeTestClass()
    {
        $currentTestClass = get_class($this);
        static $setUpBeforeTestsError = null;
        if (self::$_testClass != $currentTestClass) {
            self::$_testClass = $currentTestClass;
            $this->setLastTestNameInClass();
            try {
                $setUpBeforeTestsError = null;
                $this->setUpBeforeTests();
            } catch (Exception $e) {
                $setUpBeforeTestsError =
                    "\nError in setUpBeforeTests method for '" . $currentTestClass . "' class:\n" . $e->getMessage();
            }
            if (isset($e)) {
                throw $e;
            }
        }
        if ($setUpBeforeTestsError !== null) {
            $this->markTestSkipped($setUpBeforeTestsError);
        }
    }

    /**
     * Prepare browser session
     */
    public function prepareBrowserSession()
    {
        $browsers = $this->_configHelper->getConfigBrowsers();
        $this->setupSpecificBrowser($browsers['default']);
        $this->setBrowserUrl($this->_configHelper->getBaseUrl());
        //$this->setLogHandle($this->_testConfig->getLogFile());
        $this->session = $this->prepareSession();
    }

    final function setUp()
    {
        $this->clearMessages();
        $this->_paramsHelper = new Mage_Selenium_Helper_Params();
        $this->prepareBrowserSession();
        $this->setUpBeforeTestClass();
    }

    /**
     * Function is called before all tests in a test class
     * and can be used for some precondition(s) for all tests
     */
    public function setUpBeforeTests()
    {
    }

    /**
     * Define name of last testcase in test class
     */
    private function setLastTestNameInClass()
    {
        $testMethods = array();
        $class = new ReflectionClass(self::$_testClass);
        foreach ($class->getMethods() as $method) {
            if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                $testMethods[] = $method->getName();
            }
        }
        $testName = end($testMethods);
        $data = PHPUnit_Util_Test::getProvidedData(self::$_testClass, $testName);
        if ($data) {
            $testName .= sprintf(' with data set #%d', count($data) - 1);
        }
        self::$_lastTestNameInClass = $testName;
    }

    /**
     * Implementation of tearDownAfterAllTests() method in the object context, called as tearDownAfterTestClass()<br>
     * Used ONLY one time after execution of last test in test class
     * Implementation of tearDownAfterEachTest() method in the object context, called as tearDownAfterTest()<br>
     * Used after execution of each test in test class
     * @throws Exception
     */
    final function tearDown()
    {
        if ($this->hasFailed()) {
            if ($this->_saveHtmlPageOnFailure) {
                $this->saveHtmlPage();
            }
            if ($this->_saveScreenshotOnFailure) {
                $this->takeScreenshot();
            }
        } else {
            $this->assertEmptyVerificationErrors();
        }

        $annotations = $this->getAnnotations();
        if (!isset($annotations['method']['skipTearDown'])) {
            try {
                $this->tearDownAfterTest();
            } catch (Exception $e) {
            }
        }

        try {
            if ($this->getName() == self::$_lastTestNameInClass) {
                $this->tearDownAfterTestClass();
            }
        } catch (Exception $_e) {
            if (!isset($e)) {
                $e = $_e;
            }
        }

        if (isset($e) && !$this->hasFailed()) {
            if ($this->_saveHtmlPageOnFailure) {
                $this->saveHtmlPage();
            }
            if ($this->_saveScreenshotOnFailure) {
                $this->takeScreenshot();
            }
        }

        //if (!$this->frameworkConfig['shareSession']) {
        //    $this->drivers[0]->stopBrowserSession();
        //}

        if (isset($e)) {
            throw $e;
        }
    }

    protected function tearDownAfterTestClass()
    {
    }

    protected function tearDownAfterTest()
    {
    }

    /**
     * Access/load helpers from the tests. Helper class name should be like "TestScope_HelperName"
     *
     * @param string $testScope Part of the helper class name which refers to the file with the needed helper
     *
     * @return object
     * @throws UnexpectedValueException
     */
    protected function _loadHelper($testScope)
    {
        if (empty($testScope)) {
            throw new UnexpectedValueException('Helper name can\'t be empty');
        }

        $helpers = $this->_testConfig->getTestHelperClassNames();

        if (!isset($helpers[ucwords($testScope)])) {
            throw new UnexpectedValueException('Cannot load helper "' . $testScope . '"');
        }

        $helperClassName = $helpers[ucwords($testScope)];
        if (!isset(self::$_testHelpers[$helperClassName])) {
            if (class_exists($helperClassName)) {
                self::$_testHelpers[$helperClassName] = new $helperClassName();
            } else {
                return false;
            }
        }
        if (self::$_testHelpers[$helperClassName] instanceof Mage_Selenium_TestCase) {
            foreach (get_object_vars($this) as $name => $value) {
                self::$_testHelpers[$helperClassName]->$name = $value;
            }
        }

        return self::$_testHelpers[$helperClassName];
    }

    /**
     * Retrieve instance of helper
     *
     * @param  string $className
     *
     * @return Mage_Selenium_TestCase
     */
    public function helper($className)
    {
        $className = str_replace('/', '_', $className);
        if (strpos($className, '_Helper') === false) {
            $className .= '_Helper';
        }

        if (!isset(self::$_testHelpers[$className])) {
            if (class_exists($className)) {
                self::$_testHelpers[$className] = new $className;
            } else {
                return false;
            }
        }

        if (self::$_testHelpers[$className] instanceof Mage_Selenium_TestCase) {
            foreach (get_object_vars($this) as $name => $value) {
                self::$_testHelpers[$className]->$name = $value;
            }
        }

        return self::$_testHelpers[$className];
    }

    /**
     * @param string $message
     */
    public function skipTestWithScreenshot($message)
    {
        //$name = '';
        //foreach (debug_backtrace() as $line) {
        //    if (preg_match('/Test$/', $line['class'])) {
        //        $name = 'Skipped-' . time() . '-' . $line['class'] . '-' . $line['function'];
        //        break;
        //    }
        //}
        //$url = $this->takeScreenshot($name);
        //$this->markTestSkipped($url . $message);
        $this->markTestSkipped($message);
    }

    ################################################################################
    #                                                                              #
    #                               Assertions Methods                             #
    #                                                                              #
    ################################################################################
    /**
     * Asserts that $condition is true. Reports an error $message if $condition is false.
     * @static
     *
     * @param bool $condition Condition to assert
     * @param string|array $message Message to report if the condition is false (by default = '')
     */
    public static function assertTrue($condition, $message = '')
    {
        $message = self::messagesToString($message);

        self::assertThat($condition, self::isTrue(), $message);
    }

    /**
     * Asserts that $condition is false. Reports an error $message if $condition is true.
     * @static
     *
     * @param bool $condition Condition to assert
     * @param string $message Message to report if the condition is true (by default = '')
     */
    public static function assertFalse($condition, $message = '')
    {
        $message = self::messagesToString($message);

        self::assertThat($condition, self::isFalse(), $message);
    }

    ################################################################################
    #                                                                              #
    #                            Parameter helper methods                          #
    #                                                                              #
    ################################################################################
    /**
     * Append parameters decorator object
     *
     * @param Mage_Selenium_Helper_Params $paramsHelperObject Parameters decorator object
     *
     * @return Mage_Selenium_TestCase
     */
    public function appendParamsDecorator($paramsHelperObject)
    {
        $this->_paramsHelper = $paramsHelperObject;

        return $this;
    }

    /**
     * Add parameter to params object instance
     *
     * @param string $name
     * @param string $value
     *
     * @return Mage_Selenium_Helper_Params
     */
    public function addParameter($name, $value)
    {
        $this->_paramsHelper->setParameter($name, $value);
        return $this;
    }

    /**
     * Get  parameter from params object instance
     *
     * @param string $name
     *
     * @return string
     */
    public function getParameter($name)
    {
        return $this->_paramsHelper->getParameter($name);
    }

    /**
     * Define parameter %$paramName% from URL
     *
     * @param string $paramName
     * @param null|string $url
     *
     * @return null|string
     */
    public function defineParameterFromUrl($paramName, $url = null)
    {
        if (is_null($url)) {
            $url = self::_getMcaFromCurrentUrl($this->_configHelper->getConfigAreas(), $this->url());
        }
        $title_arr = explode('/', $url);
        if (in_array($paramName, $title_arr) && isset($title_arr[array_search($paramName, $title_arr) + 1])) {
            return $title_arr[array_search($paramName, $title_arr) + 1];
        }
        foreach ($title_arr as $key => $value) {
            if (preg_match("#$paramName$#i", $value) && isset($title_arr[$key + 1])) {
                return $title_arr[$key + 1];
            }
        }
        return null;
    }

    /**
     * Define parameter %id% from attribute @title by XPath
     *
     * @param string $locator
     *
     * @return null|string
     */
    public function defineIdFromTitle($locator)
    {
        $urlFromTitleAttribute = $this->getElement($locator)->attribute('title');
        if (is_numeric($urlFromTitleAttribute)) {
            return $urlFromTitleAttribute;
        }

        return $this->defineIdFromUrl($urlFromTitleAttribute);
    }

    /**
     * Define parameter %id% from URL
     *
     * @param null|string $url
     *
     * @return null|string
     */
    public function defineIdFromUrl($url = null)
    {
        return $this->defineParameterFromUrl('id', $url);
    }

    /**
     * Adds field ID to Message Xpath (sets %fieldId% parameter)
     *
     * @param string $fieldType Field type
     * @param string $fieldName Field name from UIMap
     */
    public function addFieldIdToMessage($fieldType, $fieldName)
    {
        $fieldXpath = $this->_getControlXpath($fieldType, $fieldName);
        if ($this->isElementPresent($fieldXpath . '[string-length(@id)>1]')) {
            $fieldId = $this->getAttribute($fieldXpath . '@id');
        } else {
            $fieldId = $this->getAttribute($fieldXpath . '@name');
        }
        $this->addParameter('fieldId', $fieldId);
    }

    ################################################################################
    #                                                                              #
    #                               Data helper methods                            #
    #                                                                              #
    ################################################################################
    /**
     * Generates random value as a string|text|email $type, with specified $length.<br>
     * Available $modifier:
     * <li>if $type = string - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = text - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = email - valid|invalid
     *
     * @param string $type Available types are 'string', 'text', 'email' (by default = 'string')
     * @param int $length Generated value length (by default = 100)
     * @param null|string $modifier Value modifier, e.g. PCRE class (by default = null)
     * @param null|string $prefix Prefix to prepend the generated value (by default = null)
     *
     * @return string
     */
    public function generate($type = 'string', $length = 100, $modifier = null, $prefix = null)
    {
        $result = $this->_dataGeneratorHelper->generate($type, $length, $modifier, $prefix);
        return $result;
    }

    /**
     * Loads test data.
     *
     * @param string $dataFile - File name or full path to file in fixture folder
     * (for example: 'default\core\Mage\AdminUser\data\AdminUsers') in which DataSet is specified
     * @param string $dataSource - DataSet name(for example: 'test_data')
     * or part of DataSet (for example: 'test_data/product')
     * @param array|null $overrideByKey
     * @param array|null $overrideByValueParam
     *
     * @throws PHPUnit_Framework_Exception
     * @return array
     */
    public function loadDataSet($dataFile, $dataSource, $overrideByKey = null, $overrideByValueParam = null)
    {
        $data = $this->_dataHelper->getDataValue($dataSource);

        if ($data === false) {
            $explodedData = explode('/', $dataSource);
            $dataSetName = array_shift($explodedData);
            $this->_dataHelper->loadTestDataSet($dataFile, $dataSetName);
            $data = $this->_dataHelper->getDataValue($dataSource);
        }

        if (!is_array($data)) {
            throw new PHPUnit_Framework_Exception('Data "' . $dataSource . '" is not specified.');
        }

        if ($overrideByKey) {
            $data = $this->overrideArrayData($overrideByKey, $data, 'byFieldKey');
        }

        if ($overrideByValueParam) {
            $data = $this->overrideArrayData($overrideByValueParam, $data, 'byValueParam');
        }

        array_walk_recursive($data, array($this, 'setDataParams'));

        return $this->clearDataArray($data);
    }

    /**
     * Override data in array.
     *
     * @param array $dataForOverride
     * @param array $overrideArray
     * @param string $overrideType
     *
     * @return array
     * @throws RuntimeException
     */
    public function overrideArrayData(array $dataForOverride, array $overrideArray, $overrideType)
    {
        $errorMessages = array();
        $messageParam = strtolower(substr_replace(str_replace('by', '', $overrideType), ' ', 5, 0));
        foreach ($dataForOverride as $fieldKey => $fieldValue) {
            if (!$this->overrideDataByCondition($fieldKey, $fieldValue, $overrideArray, $overrideType)) {
                $errorMessages[] =
                    "Value for '" . $fieldKey . "' " . $messageParam . " is not changed: [There is no this "
                    . $messageParam . " in dataset]";
            }
        }
        if ($errorMessages) {
            throw new RuntimeException(implode("\n", $errorMessages));
        }

        return $overrideArray;
    }

    /**
     * Change in array value by condition.
     *
     * @param string $overrideKey
     * @param string $overrideValue
     * @param array $overrideArray
     * @param string $condition   byFieldKey|byValueParam
     *
     * @return bool
     * @throws OutOfRangeException
     */
    public function overrideDataByCondition($overrideKey, $overrideValue, &$overrideArray, $condition)
    {
        $isOverridden = false;
        foreach ($overrideArray as $currentKey => &$currentValue) {
            switch ($condition) {
                case 'byFieldKey':
                    $isFound = ($currentKey === $overrideKey);
                    break;
                case 'byValueParam':
                    $isFound = (!is_array($currentValue)) ? preg_match(
                        '/' . preg_quote('%' . $overrideKey . '%') . '/', $currentValue) : false;
                    break;
                default:
                    throw new OutOfRangeException('Wrong condition');
                    break;
            }
            if ($isFound) {
                if ($condition == 'byValueParam') {
                    $currentValue = (!is_array($overrideValue)) ? str_replace(
                        '%' . $overrideKey . '%', $overrideValue, $currentValue) : $overrideValue;
                } else {
                    $currentValue = $overrideValue;
                }
                $isOverridden = true;
            } elseif (is_array($currentValue)) {
                $isOverridden = $this->overrideDataByCondition($overrideKey, $overrideValue, $currentValue, $condition)
                                || $isOverridden;
            }
        }
        return $isOverridden;
    }

    /**
     * Set data params
     *
     * @param string $value
     * @param string $key Index of the target to randomize
     */
    public function setDataParams(&$value, $key)
    {
        if (preg_match('/%randomize%/', $value)) {
            $value = preg_replace('/%randomize%/', $this->generate('string', 5, ':lower:'), $value);
        }
        if (preg_match('/%longValue[0-9]+%/', $value)) {
            $str = preg_replace('/(.)+(?=longValue[0-9]+%)/', '', $value);
            list($dataParam) = explode('%', $str);
            $length = preg_replace('/[^0-9]/', '', $dataParam);
            $value = preg_replace('/%longValue[0-9]+%/', $this->generate('string', $length, ':alpha:'), $value);
        }
        if (preg_match('/%specialValue[0-9]+%/', $value)) {
            $str = preg_replace('/(.)+(?=specialValue[0-9]+%)/', '', $value);
            list($dataParam) = explode('%', $str);
            $length = preg_replace('/[^0-9]/', '', $dataParam);
            $value = preg_replace('/%specialValue[0-9]+%/', $this->generate('string', $length, ':punct:'), $value);
        }
        if (preg_match('/%currentDate%/', $value)) {
            $fallbackOrderHelper = $this->_configHelper->getFixturesFallbackOrder();
            switch (end($fallbackOrderHelper)) {
                case 'default':
                    $value = preg_replace('/%currentDate%/', date("n/j/y"), $value);
                    break;
                default:
                    $value = preg_replace('/%currentDate%/', date("n/j/Y"), $value);
                    break;
            }
        }
        if (preg_match('/^%next(\w)+%$/', $value)) {
            $fallbackOrderHelper = $this->_configHelper->getFixturesFallbackOrder();
            $param = strtoupper(substr(substr($value, 0, -1), 5));
            switch (end($fallbackOrderHelper)) {
                case 'enterprise':
                    $value = preg_replace('/%next(\w)+%/', date("n/j/Y", strtotime("+1 $param")), $value);
                    break;
                default:
                    $value = preg_replace('/%next(\w)+%/', date("n/j/y", strtotime("+1 $param")), $value);
                    break;
            }
        }
    }

    /**
     * Delete field in array with special values(for example: %noValue%)
     *
     * @param array $dataArray
     *
     * @return array|bool
     */
    public function clearDataArray($dataArray)
    {
        if (!is_array($dataArray)) {
            return false;
        }

        foreach ($dataArray as $key => $value) {
            if (is_array($value)) {
                $dataArray[$key] = $this->clearDataArray($value);
                if (count($dataArray[$key]) == false) {
                    unset($dataArray[$key]);
                }
            } elseif (preg_match('/^\%(\w)+\%$/', $value)) {
                unset($dataArray[$key]);
            }
        }

        return $dataArray;
    }

    ################################################################################
    #                                                                              #
    #                               Messages helper methods                        #
    #                                                                              #
    ################################################################################

    /**
     * Removes all added messages
     *
     * @param null|string $type
     */
    public function clearMessages($type = null)
    {
        if ($type && array_key_exists($type, self::$_messages)) {
            unset(self::$_messages[$type]);
        } elseif ($type == null) {
            self::$_messages = null;
        }
    }

    /**
     * Gets all messages on the pages
     */
    protected function _parseMessages()
    {
        $area = $this->getArea();
        $page = $this->getCurrentUimapPage();
        if ($area == 'admin' || $area == 'frontend') {
            self::$_messages['notice'] = $this->getElementsValue($page->findMessage('general_notice'), 'text');
            //@TODO add field name to validation messages
            //$fieldNameWithMessage = $page->findPageelement('fieldNameWithValidationMessage');
            self::$_messages['validation'] = $this->getElementsValue($page->findMessage('general_validation'), 'text');
        } else {
            self::$_messages['validation'] = $this->getElementsValue($page->findMessage('general_validation'), 'text');
        }
        self::$_messages['success'] = $this->getElementsValue($page->findMessage('general_success'), 'text');
        self::$_messages['error'] = $this->getElementsValue($page->findMessage('general_error'), 'text');
    }

    /**
     * Returns all messages (or messages of the specified type) on the page
     *
     * @param null|string $messageType Message type: validation|error|success
     *
     * @return array
     */
    public function getMessagesOnPage($messageType = null)
    {
        $this->_parseMessages();
        if ($messageType) {
            if (is_string($messageType)) {
                $messageType = explode(',', $messageType);
                $messageType = array_map('trim', $messageType);
            }
            $messages = array();
            foreach ($messageType as $message) {
                if (isset(self::$_messages[$message])) {
                    $messages = array_merge($messages, self::$_messages[$message]);
                }
            }
            return $messages;
        }

        return self::$_messages;
    }

    /**
     * Returns all parsed messages (or messages of the specified type)
     *
     * @param null|string $type Message type: validation|error|success (default = null, for all messages)
     *
     * @return array|null
     */
    public function getParsedMessages($type = null)
    {
        if ($type) {
            return (isset(self::$_messages[$type])) ? self::$_messages[$type] : null;
        }
        return self::$_messages;
    }

    /**
     * Adds validation|error|success message(s)
     *
     * @param string $type Message type: validation|error|success
     * @param string|array $message Message text
     */
    public function addMessage($type, $message)
    {
        if (is_array($message)) {
            foreach ($message as $value) {
                self::$_messages[$type][] = $value;
            }
        } else {
            self::$_messages[$type][] = $message;
        }
    }

    /**
     * Adds a verification message
     *
     * @param string|array $message Message text
     */
    public function addVerificationMessage($message)
    {
        $this->addMessage('verification', $message);
    }


    /**
     * Verifies messages count
     *
     * @param int $count Expected number of message(s) on the page
     * @param null|string $locator XPath of a message(s) that should be evaluated (default = null)
     *
     * @return int Number of nodes that match the specified $xpath
     */
    public function verifyMessagesCount($count = 1, $locator = null)
    {
        if ($locator === null) {
            $locator = $this->_getMessageXpath('general_validation');
        }
        $this->_parseMessages();
        return count($this->getElements($locator)) == $count;
    }

    /**
     * Check if the specified message exists on the page
     *
     * @param string $message Message ID from UIMap
     *
     * @return array
     */
    public function checkMessage($message)
    {
        $messageLocator = $this->_getMessageXpath($message);
        return $this->checkMessageByXpath($messageLocator);
    }

    /**
     * Checks if  message with the specified XPath exists on the page
     *
     * @param string $locator XPath of message to checking
     *
     * @return array
     */
    public function checkMessageByXpath($locator)
    {
        if ($locator && $this->elementIsPresent($locator)) {
            return array('success' => true);
        }
        $this->_parseMessages();
        return array('success' => false, 'locator' => $locator,
                     'found'   => self::messagesToString($this->getMessagesOnPage()));
    }

    /**
     * Checks if any 'error' message exists on the page
     *
     * @param null|string $message Error message ID from UIMap OR XPath of the error message (by default = null)
     *
     * @return array
     */
    public function errorMessage($message = null)
    {
        return (!empty($message)) ? $this->checkMessage($message)
            : $this->checkMessageByXpath($this->_getMessageXpath('general_error'));
    }

    /**
     * Checks if any 'success' message exists on the page
     *
     * @param null|string $message Success message ID from UIMap OR XPath of the success message (by default = null)
     *
     * @return array
     */
    public function successMessage($message = null)
    {
        return (!empty($message)) ? $this->checkMessage($message)
            : $this->checkMessageByXpath($this->_getMessageXpath('general_success'));
    }

    /**
     * Checks if any 'validation' message exists on the page
     *
     * @param null|string $message Validation message ID from UIMap OR XPath of the validation message (by default = null)
     *
     * @return array
     */
    public function validationMessage($message = null)
    {
        return (!empty($message)) ? $this->checkMessage($message)
            : $this->checkMessageByXpath($this->_getMessageXpath('general_validation'));
    }

    /**
     * Asserts that the specified message of the specified type is present on the current page
     *
     * @param string $type success|validation|error
     * @param null|string $message Message ID from UIMap
     */
    public function assertMessagePresent($type, $message = null)
    {
        $method = strtolower($type) . 'Message';
        $result = $this->$method($message);
        if (!$result['success']) {
            $location = 'Current url: \'' . $this->url() . "'\nCurrent page: '" . $this->getCurrentPage() . "'\n";
            if (is_null($message)) {
                $error = "Failed looking for '" . $type . "' message.\n";
            } else {
                $error = "Failed looking for '" . $message . "' message.\n[locator: " . $result['locator'] . "]\n";
            }
            if ($result['found']) {
                $error .= "Found  messages instead:\n" . $result['found'];
            }
            $this->fail($location . $error);
        }
    }

    /**
     * Asserts that the specified message of the specified type is not present on the current page
     *
     * @param string $type success|validation|error
     * @param null|string $message Message ID from UIMap
     */
    public function assertMessageNotPresent($type, $message = null)
    {
        $method = strtolower($type) . 'Message';
        $result = $this->$method($message);
        if ($result['success']) {
            if (is_null($message)) {
                $error = "'" . $type . "' message is on the page.";
            } else {
                $error = "'" . $message . "' message is on the page.";
            }
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            if ($messagesOnPage) {
                $error .= "\n" . $messagesOnPage;
            }
            $this->fail($error);
        }
    }

    /**
     * Assert there are no verification errors
     */
    public function assertEmptyVerificationErrors()
    {
        $verificationErrors = $this->getParsedMessages('verification');
        if ($verificationErrors) {
            $this->clearMessages('verification');
            $this->fail(implode("\n", $verificationErrors));
        }
    }

    /**
     * Returns a string representation of the messages.
     *
     * @static
     *
     * @param array|string $message
     *
     * @return string
     */
    protected static function messagesToString($message)
    {
        if (is_array($message) && $message) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }
        return $message;
    }

    ################################################################################
    #                                                                              #
    #                               Navigation helper methods                      #
    #                                                                              #
    ################################################################################
    /**
     * Set additional params for navigation
     *
     * @param string $params your params to add to URL (?paramName1=paramValue1&paramName2=paramValue2)
     */
    public function setUrlPostfix($params)
    {
        $this->_urlPostfix = $params;
    }

    /**
     * Navigates to the specified page in specified area.<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $area Area identifier (by default = 'frontend')
     * @param string $page Page identifier
     * @param bool $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function goToArea($area = 'frontend', $page = '', $validatePage = true)
    {
        $this->_configHelper->setArea($area);
        if ($page == '') {
            $areaConfig = $this->_configHelper->getAreaConfig();
            $page = $areaConfig['base_page_uimap'];
        }
        $this->navigate($page, $validatePage);
        return $this;
    }

    /**
     * Navigates to the specified page in the current area.<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $page Page identifier
     * @param bool $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page, $validatePage = true)
    {
        $area = $this->_configHelper->getArea();
        $clickLocator = $this->_uimapHelper->getPageClickXpath($area, $page, $this->_paramsHelper);
        $availableElement = ($clickLocator) ? $this->elementIsPresent($clickLocator) : false;
        if ($availableElement) {
            $this->url($availableElement->attribute('href'));
        } else {
            $url = $this->_uimapHelper->getPageUrl($area, $page, $this->_paramsHelper);
            $url = isset($this->_urlPostfix) ? $url . $this->_urlPostfix : $url;
            $this->url($url);
        }
        if ($validatePage) {
            $this->validatePage($page);
        }

        return $this;
    }

    /**
     * Navigate to the specified admin page.<br>
     * Page identifier must be described in the UIMap. Opens "Dashboard" page by default.
     *
     * @param string $page Page identifier (by default = 'dashboard')
     * @param bool $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function admin($page = 'dashboard', $validatePage = true)
    {
        $this->goToArea('admin', $page, $validatePage);
        return $this;
    }

    /**
     * Navigate to the specified frontend page<br>
     * Page identifier must be described in the UIMap. Opens "Home page" by default.
     *
     * @param string $page Page identifier (by default = 'home_page')
     * @param bool $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function frontend($page = 'home_page', $validatePage = true)
    {
        $this->goToArea('frontend', $page, $validatePage);
        return $this;
    }

    /**
     * @return string
     */
    public function selectLastWindow()
    {
        $names = $this->getAllWindowNames();
        $popupId = end($names);
        $this->waitForPopUp($popupId, $this->_browserTimeoutPeriod);
        $this->selectWindow("name=" . $popupId);

        return $popupId;
    }

    /**
     *
     */
    public function closeLastWindow()
    {
        $windowQty = $this->getAllWindowNames();
        $popupId = end($windowQty);
        if (count($windowQty) > 1 && $popupId != 'null') {
            $this->selectWindow("name=" . $popupId);
            $this->close();
            $this->selectWindow(null);
        }
    }

    ################################################################################
    #                                                                              #
    #                                Area helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Gets current location area<br>
     * Usage: define area currently operating.
     * <li>Possible areas: frontend | admin
     * @return string
     */
    public function getCurrentLocationArea()
    {
        $currentArea = self::_getAreaFromCurrentUrl($this->_configHelper->getConfigAreas(), $this->url());
        $this->_configHelper->setArea($currentArea);
        return $currentArea;
    }

    /**
     * Find area in areasConfig using full page URL
     * @static
     *
     * @param array $areasConfig Full area config
     * @param string $currentUrl Full URL to page
     *
     * @return string
     * @throws RuntimeException
     */
    protected static function _getAreaFromCurrentUrl($areasConfig, $currentUrl)
    {
        $currentArea = '';
        $currentUrl = preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $currentUrl));
        $possibleAreas = array();
        foreach ($areasConfig as $area => $areaConfig) {
            $areaUrl =
                preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $areaConfig['url']));
            if (strpos($currentUrl, $areaUrl) === 0) {
                $possibleAreas[$area] = $areaUrl;
            }
        }
        $count = 1;
        foreach ($possibleAreas as $area => $areaUrl) {
            $length = strlen($areaUrl);
            if ($length > $count) {
                $count = $length;
                $currentArea = $area;
            }
        }
        if ($currentArea == '') {
            throw new RuntimeException('Area is not defined for ulr:  ' . $currentUrl);
        }
        return $currentArea;
    }

    /**
     * Set current area
     *
     * @param string $name
     *
     * @return Mage_Selenium_TestCase
     */
    public function setArea($name)
    {
        $this->_configHelper->setArea($name);
        return $this;
    }

    /**
     * Return current area name
     * @return string
     * @throws OutOfRangeException
     */
    public function getArea()
    {
        return $this->_configHelper->getArea();
    }

    /**
     * Return current application config
     * @return array
     * @throws OutOfRangeException
     */
    public function getApplicationConfig()
    {
        return $this->_configHelper->getApplicationConfig();
    }

    ################################################################################
    #                                                                              #
    #                       UIMap of Page helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Retrieves Page data from UIMap by $pageKey
     *
     * @param string $area Area identifier
     * @param string $pageKey UIMap page key
     *
     * @return Mage_Selenium_Uimap_Page
     */
    public function getUimapPage($area, $pageKey)
    {
        return $this->_uimapHelper->getUimapPage($area, $pageKey, $this->_paramsHelper);
    }

    /**
     * Retrieves current Page data from UIMap.
     * Gets current page name from an internal variable.
     * @return Mage_Selenium_Uimap_Page
     */
    public function getCurrentUimapPage()
    {
        return $this->getUimapPage($this->_configHelper->getArea(), $this->getCurrentPage());
    }

    /**
     * Retrieves current Page data from UIMap.
     * Gets current page name from the current URL.
     * @return Mage_Selenium_Uimap_Page
     */
    public function getCurrentLocationUimapPage()
    {
        $areasConfig = $this->_configHelper->getConfigAreas();
        $currentUrl = $this->url();
        $mca = self::_getMcaFromCurrentUrl($areasConfig, $currentUrl);
        $area = self::_getAreaFromCurrentUrl($areasConfig, $currentUrl);
        return $this->_uimapHelper->getUimapPageByMca($area, $mca, $this->_paramsHelper);
    }

    ################################################################################
    #                                                                              #
    #                             Page ID helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Change current page
     *
     * @param string $page
     *
     * @return Mage_Selenium_TestCase
     */
    public function setCurrentPage($page)
    {
        $this->_configHelper->setCurrentPageId($page);
        return $this;
    }

    /**
     * Get current page
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_configHelper->getCurrentPageId();
    }

    /**
     * Find PageID in UIMap in the current area using full page URL
     *
     * @param string|null $url Full URL
     *
     * @return string
     */
    protected function _findCurrentPageFromUrl($url = null)
    {
        if (is_null($url)) {
            $url = str_replace($this->_urlPostfix, '', $this->url());
        }
        $areasConfig = $this->_configHelper->getConfigAreas();
        $mca = self::_getMcaFromCurrentUrl($areasConfig, $url);
        $area = self::_getAreaFromCurrentUrl($areasConfig, $url);
        $page = $this->_uimapHelper->getUimapPageByMca($area, $mca, $this->_paramsHelper);

        return $page->getPageId();
    }

    /**
     * Checks if the currently opened page is $page.<br>
     * Returns true if the specified page is the current page, otherwise returns false and sets the error message:
     * "Opened the wrong page: $currentPage (should be:$page)".<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $page Page identifier
     *
     * @return bool
     */
    public function checkCurrentPage($page)
    {
        $currentPage = $this->_findCurrentPageFromUrl();
        if ($currentPage != $page) {
            $this->addVerificationMessage("Opened the wrong page '" . $currentPage . "'(should be: '" . $page . "')");
            return false;
        }
        return true;
    }

    /**
     * Validates properties of the current page.
     *
     * @param string $page Page identifier
     */
    public function validatePage($page = '')
    {
        $this->getCurrentLocationArea();
        if ($page) {
            $this->assertTrue($this->checkCurrentPage($page), $this->getMessagesOnPage());
        } else {
            $page = $this->_findCurrentPageFromUrl();
        }
        //$this->assertTextNotPresent('Fatal error', 'Fatal error on page');
        //$this->assertTextNotPresent('There has been an error processing your request',
        //    'Fatal error on page: \'There has been an error processing your request\'');
        //$this->assertTextNotPresent('Notice:', 'Notice error on page');
        //$this->assertTextNotPresent('Parse error', 'Parse error on page');
        //if (!$this->controlIsPresent('message', 'general_notice')) {
        //    $this->assertTextNotPresent('Warning:', 'Warning on page');
        //}
        //$this->assertTextNotPresent('If you typed the URL directly', 'The requested page was not found.');
        //$this->assertTextNotPresent('was not found', 'Something was not found:)');
        //$this->assertTextNotPresent('Service Temporarily Unavailable', 'Service Temporarily Unavailable');
        //$this->assertTextNotPresent('The page isn\'t redirecting properly', 'The page isn\'t redirecting properly');
        //$this->assertTextNotPresent('Internal server error', 'HTTP Error 500 Internal server error');
        $expectedTitle = $this->getUimapPage($this->_configHelper->getArea(), $page)->getTitle($this->_paramsHelper);
        if (!is_null($expectedTitle)) {
            $this->assertSame($expectedTitle, $this->title(),
                'Current url: \'' . $this->url() . "\n" . 'Title for page "' . $page . '" is unexpected.');
        }
        $this->setCurrentPage($page);
    }

    ################################################################################
    #                                                                              #
    #                       Page Elements helper methods                           #
    #                                                                              #
    ################################################################################

    /**
     * Get Module-Controller-Action-part of page URL
     * @static
     *
     * @param array $areasConfig Full area config
     * @param string $currentUrl Current URL
     *
     * @return string
     */
    protected static function _getMcaFromCurrentUrl($areasConfig, $currentUrl)
    {
        $mca = '';
        $currentArea = self::_getAreaFromCurrentUrl($areasConfig, $currentUrl);
        $baseUrl = preg_replace('|^www\.|', '', preg_replace('|^http([s]{0,1})://|', '',
            preg_replace('|/index.php/?|', '/', $areasConfig[$currentArea]['url'])));
        $currentUrl = preg_replace('|^www\.|', '',
            preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $currentUrl)));

        if (strpos($currentUrl, $baseUrl) !== false) {
            $mca = trim(substr($currentUrl, strlen($baseUrl)), " /\\");
        }

        if ($mca && $mca[0] != '/') {
            $mca = '/' . $mca;
        }

        //Removes part of url that appears after pressing "Reset Filter" or "Search" button in grid
        //(when not using ajax to reload the page)
        $mca = preg_replace('|/filter/((\S)+)?/form_key/[A-Za-z0-9]+/?|', '/', $mca);
        //Delete secret key from url
        $mca = preg_replace('|/(index/)?key/[A-Za-z0-9]+/?|', '/', $mca);
        //Delete action part of mca if it's index
        $mca = preg_replace('|/index/?$|', '/', $mca);
        //Delete action part of mca if it's ?SID=
        $mca = preg_replace('|(\?)?SID=([a-zA-Z\d]+)?|', '', $mca);
        //@TODO Temporary fix for magento2
        $mca = preg_replace('/^(\/)?(admin|backend)(\/)?/', '', $mca);

        return preg_replace('|^/|', '', $mca);
    }

    /**
     * Get URL of the specified page
     *
     * @param string $area Application area
     * @param string $page UIMap page key
     *
     * @return string
     */
    public function getPageUrl($area, $page)
    {
        return $this->_uimapHelper->getPageUrl($area, $page, $this->_paramsHelper);
    }

    /**
     * Get part of UIMap for specified uimap element(does not use for 'message' element)
     *
     * @param string $elementType
     * @param string $elementName
     * @param Mage_Selenium_Uimap_Page|null $uimap
     *
     * @return Mage_Selenium_Uimap_Fieldset|Mage_Selenium_Uimap_Tab
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    protected function _findUimapElement($elementType, $elementName, $uimap = null)
    {
        $fieldSetsNotInTab = null;
        $errorMessage = null;
        $returnValue = null;
        if (is_null($uimap)) {
            if ($elementType == 'button') {
                $generalButtons = $this->getCurrentUimapPage()->getMainButtons();
                if (isset($generalButtons[$elementName])) {
                    return $generalButtons[$elementName];
                }
            }
            if ($elementType != 'fieldset' && $elementType != 'tab') {
                $uimap = $this->_getActiveTabUimap();
                if (is_null($uimap)) {
                    $uimap = $this->getCurrentUimapPage();
                } else {
                    $mainForm = $this->getCurrentUimapPage()->getMainForm();
                    $fieldSetsNotInTab = $mainForm->getMainFormFieldsets();
                }
            } else {
                $uimap = $this->getCurrentUimapPage();
            }
        }
        $method = 'find' . ucfirst(strtolower($elementType));
        try {
            $returnValue = $uimap->$method($elementName, $this->_paramsHelper);
        } catch (Exception $e) {
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            $errorMessage =
                'Current location url: ' . $this->url() . "\n" . 'Current page "' . $this->getCurrentPage() . '": '
                . $e->getMessage() . ' - "' . $elementName . '"';
            if (strlen($messagesOnPage) > 0) {
                $errorMessage .= "\nMessages on current page:\n" . $messagesOnPage;
            }
        }
        if (isset($e) && $fieldSetsNotInTab != null) {
            foreach ($fieldSetsNotInTab as $fieldset) {
                try {
                    $returnValue = $fieldset->$method($elementName, $this->_paramsHelper);
                } catch (Exception $_e) {
                }
            }
        }
        if ($errorMessage != null && $returnValue === null) {
            throw new PHPUnit_Framework_AssertionFailedError($errorMessage);
        }
        return $returnValue;
    }

    /**
     * Get part of UIMap for opened tab
     * @return Mage_Selenium_Uimap_Tab
     */
    protected function _getActiveTabUimap()
    {
        $tabData = $this->getCurrentUimapPage()->getAllTabs($this->_paramsHelper);
        foreach ($tabData as $tabName => $tabUimap) {
            $tabClass = $this->getControlAttribute('tab', $tabName, 'class');
            if (preg_match('/active/', $tabClass)) {
                return $tabUimap;
            }
        }
        return null;
    }

    /**
     * Gets XPath of a control with the specified name and type.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param mixed $uimap
     *
     * @return string
     */
    protected function _getControlXpath($controlType, $controlName, $uimap = null)
    {
        if ($controlType === 'message') {
            return $this->_getMessageXpath($controlName);
        }
        $xpath = $this->_findUimapElement($controlType, $controlName, $uimap);
        if (is_object($xpath) && method_exists($xpath, 'getXPath')) {
            $xpath = $xpath->getXPath($this->_paramsHelper);
        }

        return $xpath;
    }

    /**
     * Get control attribute
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param string $attribute
     *
     * @return string
     */
    public function getControlAttribute($controlType, $controlName, $attribute)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $element = $this->getElement($locator);
        switch ($attribute) {
            case 'selectedValue':
                if ($controlType == 'dropdown') {
                    $elementValue = $this->getSelectedValue($locator);
                } elseif ($controlType == 'multiselect') {
                    $elementValue = $this->getSelectedValues($locator);
                } else {
                    $elementValue = $element->attribute('value');
                }
                break;
            case 'selectedId':
                if ($controlType == 'dropdown') {
                    $elementValue = $this->getSelectedId($locator);
                } elseif ($controlType == 'multiselect') {
                    $elementValue = $this->getSelectedIds($locator);
                } else {
                    $elementValue = $element->attribute('id');
                }
                break;
            case 'selectedLabel':
                if ($controlType == 'dropdown') {
                    $elementValue = $this->getSelectedLabel($locator);
                } elseif ($controlType == 'multiselect') {
                    $elementValue = $this->getSelectedLabels($locator);
                } else {
                    $elementValue = $element->text();
                }
                break;
            case 'value':
                $elementValue = $element->value();
                break;
            case 'text':
                $elementValue = $element->text();
                break;
            default:
                $elementValue = $element->attribute($attribute);
                break;
        }

        if (is_null($elementValue)) {
            $this->fail("$controlType with name '$controlName' and locator '$locator'"
                        . " is not contains attribute '$attribute'");
        }
        if (is_array($elementValue)) {
            $elementValue = array_map('trim', $elementValue);
        } else {
            $elementValue = trim($elementValue);
        }
        return $elementValue;
    }

    /**
     * Gets XPath of a message with the specified name.
     *
     * @param string $message Name of a message from UIMap
     *
     * @return string
     * @throws RuntimeException
     */
    protected function _getMessageXpath($message)
    {
        $messages = $this->getCurrentUimapPage()->getAllElements('messages');
        $messageLocator = $messages->get($message, $this->_paramsHelper);
        if ($messageLocator === null) {
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            $errorMessage =
                'Current location url: ' . $this->url() . "\n" . 'Current page "' . $this->getCurrentPage() . '": '
                . 'Message "' . $message . '" is not found';
            if (strlen($messagesOnPage) > 0) {
                $errorMessage .= "\nMessages on current page:\n" . $messagesOnPage;
            }
            throw new RuntimeException($errorMessage);
        }
        return $messageLocator;
    }

    /**
     * Gets map data values to UIPage form
     *
     * @param mixed $fieldsets Array of fieldsets to fill
     * @param array $data Array of data to fill
     *
     * @return array
     */
    protected function _getFormDataMap($fieldsets, $data)
    {
        $dataMap = array();
        $fieldsetsElements = array();
        foreach ($fieldsets as $fieldsetName => $fieldsetContent) {
            $fieldsetsElements[$fieldsetName] = $fieldsetContent->getFieldsetElements();
        }
        foreach ($data as $dataFieldName => $dataFieldValue) {
            if ($dataFieldValue == '%noValue%' || is_array($dataFieldValue)) {
                continue;
            }
            foreach ($fieldsetsElements as $fieldsetContent) {
                foreach ($fieldsetContent as $fieldsType => $fieldsData) {
                    if (array_key_exists($dataFieldName, $fieldsData)) {
                        $dataMap[$dataFieldName] = array('type'  => $fieldsType, 'value' => $dataFieldValue,
                                                         'path'  => $fieldsData[$dataFieldName],);
                        break 2;
                    }
                }
            }
        }
        return $dataMap;
    }

    /**
     * Gets map data values from Fieldset/Tab
     *
     * @param array $dataToFill
     * @param string $containerType fieldset|tab
     * @param string $containerName
     *
     * @return array
     */
    protected function getDataMapForFill(array $dataToFill, $containerType, $containerName)
    {
        $containerUimap = $this->_findUimapElement($containerType, $containerName);
        $getMethod = 'get' . ucfirst(strtolower($containerType)) . 'Elements';
        $containerElements = $containerUimap->$getMethod($this->_paramsHelper);
        $fillData = array();
        foreach ($dataToFill as $fieldName => $fieldValue) {
            if ($fieldValue == '%noValue%' || is_array($fieldValue)) {
                $fillData['skipped'][$fieldName] = $fieldValue;
                continue;
            }
            foreach ($containerElements as $elementType => $elementsData) {
                if (array_key_exists($fieldName, $elementsData)) {
                    $fillData['isPresent'][] =
                        array('type'    => $elementType, 'name' => $fieldName, 'value' => $fieldValue,
                              'locator' => $elementsData[$fieldName]);
                    continue 2;
                }
            }
            $fillData['isNotPresent'][$fieldName] = $fieldValue;
        }

        return $fillData;
    }

    ################################################################################
    #                                                                              #
    #                           Framework helper methods                           #
    #                                                                              #
    ################################################################################

    /**
     * Returns HTTP response for the specified URL.
     *
     * @param string $url
     *
     * @return array
     *
     * @throws RuntimeException when an internal CURL error happens
     */
    public function getHttpResponse($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($curl);
        $info = curl_getinfo($curl);
        if (!$info) {
            throw new RuntimeException("CURL error when accessing '$url': " . curl_error($curl));
        }
        curl_close($curl);
        return $info;
    }

    /**
     * Verifies if an external service is available.
     *
     * @param string $url
     *
     * @return bool True if the response is 200 or redirects to a such page. False otherwise.
     */
    public function httpResponseIsOK($url)
    {
        $maxRedirects = 100;
        $response = null;
        do {
            $response = $this->getHttpResponse($url);
            $url = ($response['http_code'] == 301) ? $response['redirect_url'] : null;
            $maxRedirects--;
        } while ($url && $maxRedirects > 0);
        return $response['http_code'] == 200;
    }

    /**
     * SavesHTML content of the current page and return information about it.
     * Return an empty string if the screenshotPath property is empty.
     *
     * @param null|string $fileName
     *
     * @return string
     */
    public function saveHtmlPage($fileName = null)
    {
        $screenshotPath = $this->getScreenshotPath();
        if (empty($screenshotPath)) {
            $this->fail('Screenshot Path is empty');
        }

        if ($fileName == null) {
            $fileName = time() . '-' . get_class($this) . '-' . $this->getName();
            $fileName = preg_replace('/ /', '_', preg_replace('/"/', '\'', $fileName));
            $fileName = preg_replace('/_with_data_set/', '-set', $fileName);
        }
        $filePath = $screenshotPath . $fileName . '.html';
        $file = fopen($filePath, 'a+');
        fputs($file, $this->source());
        fflush($file);
        fclose($file);
        return 'HTML Page: ' . $filePath . "\n";
    }

    /**
     * Take a screenshot and return information about it.
     * Return an empty string if the screenshotPath property is empty.
     *
     * @param null|string $fileName
     *
     * @return string
     */
    public function takeScreenshot($fileName = null)
    {
        $screenshotPath = $this->getScreenshotPath();
        if (empty($screenshotPath)) {
            $this->fail('Screenshot Path is empty');
        }

        if ($fileName == null) {
            $fileName = time() . '-' . get_class($this) . '-' . $this->getName();
            $fileName = preg_replace('/ /', '_', preg_replace('/"/', '\'', $fileName));
            $fileName = preg_replace('/_with_data_set/', '-set', $fileName);
        }
        $filePath = $screenshotPath . $fileName . '.png';
        $screenshotContent = $this->currentScreenshot();
        $file = fopen($filePath, 'a+');
        fputs($file, $screenshotContent);
        fflush($file);
        fclose($file);
        if (file_exists($filePath)) {
            return 'Screenshot: ' . $filePath . ".png\n";
        }
        return '';
    }

    /**
     * Operation System definition
     *
     * @return string Windows|Linux|MacOS|Unknown OS
     */
    public function detectOS()
    {

        $osName = $this->getEval('navigator.userAgent');
        if (preg_match('/Windows/i', $osName)) {
            return 'Windows';
        } elseif (preg_match('/Linux/i', $osName)) {
            return 'Linux';
        } elseif (preg_match('/Macintosh/i', $osName)) {
            return 'MacOS';
        }
        return 'Unknown OS';
    }

    //    /**
    //     * Get TestCase Id
    //     *
    //     * @return string
    //     */
    //    public function getTestId()
    //    {
    //        return $this->testId;
    //    }
    //
    //    /**
    //     * Set test case Id
    //     *
    //     * @param $testId
    //     *
    //     * @return Mage_Selenium_TestCase
    //     */
    //    public function setTestId($testId)
    //    {
    //        $this->drivers[0]->setTestId($testId);
    //        $this->testId = $testId;
    //        return $this;
    //    }

    /**
     * Returns correct path to screenshot save path.
     *
     * @return string
     */
    public function getScreenshotPath()
    {
        $path = $this->_screenshotPath;

        if (!in_array(substr($path, strlen($path) -1, 1), array("/","\\"))) {
            $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    /**
     * Set screenshot path (current test)
     *
     * @param $path
     *
     * @return Mage_Selenium_TestCase
     */
    public function setScreenshotPath($path)
    {
        $this->_screenshotPath = $path;
        return $this;
    }

    /**
     * Set default screenshot path (config)
     *
     * @param string $path
     *
     * @return Mage_Selenium_TestCase
     */
    public function setDefaultScreenshotPath($path)
    {
        $this->_configHelper->setScreenshotDir($path);
        $this->setScreenshotPath($path);

        return $this;
    }

    /**
     * Get default screenshot path (config)
     *
     * @return string
     */
    public function getDefaultScreenshotPath()
    {
        return $this->_configHelper->getScreenshotDir();
    }

    /**
     * Clicks a control with the specified name and type.
     *
     * @param string $controlType Type of control (e.g. button|link|radiobutton|checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickControl($controlType, $controlName, $willChangePage = true)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $availableElement = $this->elementIsPresent($locator);
        if (!$availableElement || !$availableElement->displayed()) {
            $this->fail("Current location url: '" . $this->url() . "'\nCurrent page: '" . $this->getCurrentPage()
                        . "'\nProblem with $controlType '$controlName', xpath '$locator':\n"
                        . 'Control is not present on the page');
        }
        $availableElement->click();
        if ($willChangePage) {
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->addParameter('id', $this->defineIdFromUrl());
            $this->validatePage();
        }
        return $this;
    }

    /**
     * Click a button with the specified name
     *
     * @param string $button Name of a control from UIMap
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickButton($button, $willChangePage = true)
    {
        return $this->clickControl('button', $button, $willChangePage);
    }

    /**
     * Clicks a control with the specified name and type
     * and confirms the confirmation popup with the specified message.
     *
     * @param string $controlType Type of control (e.g. button|link)
     * @param string $controlName Name of a control from UIMap
     * @param string $message Confirmation message
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     *                             in page reloading, should be false (by default = true).
     */
    public function clickControlAndConfirm($controlType, $controlName, $message, $willChangePage = true)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $availableElement = $this->elementIsPresent($locator);
        if ($availableElement) {
            $confirmation = $this->_getMessageXpath($message);
            $availableElement->click();
            $actualText = $this->alertText();
            if ($actualText == $confirmation) {
                $this->acceptAlert();
                if ($willChangePage) {
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->validatePage();
                }
            } else {
                $this->fail("The confirmation text incorrect: '$actualText' != '$confirmation''");
            }
        } else {
            $this->fail("There is no way to click on control(There is no '$controlName' $controlType)");
        }
    }

    /**
     * Submit form and confirm the confirmation popup with the specified message.
     *
     * @param string $buttonName Name of a button from UIMap
     * @param string $message Confirmation message id from UIMap
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     *                             in page reloading, should be false (by default = true).
     */
    public function clickButtonAndConfirm($buttonName, $message, $willChangePage = true)
    {
        $this->clickControlAndConfirm('button', $buttonName, $message, $willChangePage);
    }

    /**
     * Searches a control with the specified name and type on the page.
     * If the control is present, returns true; otherwise false.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return bool
     */
    public function controlIsPresent($controlType, $controlName)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        if ($this->elementIsPresent($locator)) {
            return true;
        }

        return false;
    }

    /**
     * Searches a control with the specified name and type on the page.
     * If the control is visible, returns true; otherwise false.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return bool|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function controlIsVisible($controlType, $controlName)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $availableElement = $this->elementIsPresent($locator);
        if ($availableElement && $availableElement->displayed()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return bool|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function controlIsEditable($controlType, $controlName)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $availableElement = $this->elementIsPresent($locator);
        if ($availableElement && $availableElement->enabled()) {
            return true;
        }

        return false;
    }

    /**
     * Searches a button with the specified name on the page.
     * If the button is present, returns true; otherwise false.
     *
     * @param string $button Name of a button from UIMap
     *
     * @return bool
     */
    public function buttonIsPresent($button)
    {
        return $this->controlIsPresent('button', $button);
    }

    /**
     * Open tab
     *
     * @param string $tabName tab id from uimap
     *
     * @throws OutOfRangeException
     */
    public function openTab($tabName)
    {
        $waitAjax = false;
        $isTabOpened = $this->getControlAttribute('tab', $tabName, 'class');
        if (!preg_match('/active/', $isTabOpened)) {
            if (preg_match('/ajax/', $isTabOpened)) {
                $waitAjax = true;
            }
            $this->clickControl('tab', $tabName, false);
            if ($waitAjax) {
                $this->pleaseWait();
            }
        }
    }

    /**
     * Gets all element(s) by XPath
     *
     * @param string $xpath General XPath of looking up element(s)
     * @param string $get What to get. Allowed params: 'text' or 'value' (by default = 'text')
     * @param string $additionalXPath Additional XPath (by default= '')
     *
     * @return array
     * @throws OutOfRangeException
     */
    public function getElementsByXpath($xpath, $get = 'text', $additionalXPath = '')
    {
        $elements = array();

        if (!empty($xpath)) {
            $totalElements = $this->getXpathCount($xpath);
            $pos = stripos(trim($xpath), 'css=');
            for ($i = 1; $i < $totalElements + 1; $i++) {
                if ($pos !== false && $pos == 0) {
                    $x = $xpath . ':nth(' . ($i - 1) . ')';
                } else {
                    $x = $xpath . '[' . $i . ']';
                }
                switch ($get) {
                    case 'value' :
                        $element = $this->getValue($x);
                        break;
                    case 'text' :
                        $element = $this->getText($x);
                        break;
                    default :
                        throw new OutOfRangeException('Possible values of the variable $get only "text" and "value"');
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
                            $element = '"' . $label . '": ' . $element;
                        }
                    }

                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Gets an element by XPath
     *
     * @param string $xpath XPath of an element to look up
     * @param string $get What to get. Allowed params: 'text' or 'value' (by default = 'text')
     *
     * @return mixed
     */
    public function getElementByXpath($xpath, $get = 'text')
    {
        $elements = $this->getElementsByXpath($xpath, $get);
        return array_shift($elements);
    }

    /**
     * Returns number of nodes that match the specified xPath|css selector,
     * eg. "table" would give number of tables.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param string $xpath
     *
     * @return string
     */
    public function getControlCount($controlType, $controlName, $xpath = null)
    {
        if (is_null($xpath)) {
            $xpath = $this->_getControlXpath($controlType, $controlName);
        }
        if (preg_match('/^css=/', $xpath)) {
            return $this->getCssCount($xpath);
        }
        return $this->getXpathCount($xpath);
    }

    /**
     * Returns table column names
     *
     * @param string $tableXpath
     *
     * @return array
     */
    public function getTableHeadRowNames($tableXpath = '//table[@id]')
    {
        $xpath = $tableXpath . "//tr[normalize-space(@class)='headings']";
        if (!$this->isElementPresent($xpath)) {
            $this->fail('Incorrect table head xpath: ' . $xpath);
        }

        $cellNum = $this->getXpathCount($xpath . '/th');
        $headNames = array();
        for ($cell = 0; $cell < $cellNum; $cell++) {
            $cellLocator = $tableXpath . '.0.' . $cell;
            $headNames[$cell] = $this->getTable($cellLocator);
        }
        return array_diff($headNames, array(''));
    }

    /**
     * Returns table column ID based on the column name.
     *
     * @param string $columnName
     * @param string $tableXpath
     *
     * @return int
     */
    public function getColumnIdByName($columnName, $tableXpath = '//table[@id]')
    {
        return array_search($columnName, $this->getTableHeadRowNames($tableXpath)) + 1;
    }

    /**
     * Combine elements to one xPath or css element
     * @TODO verify work with css
     * @static
     *
     * @param array $locators
     *
     * @return string
     * @throws RuntimeException
     */
    static function combineLocatorsToOne(array $locators)
    {
        $values = array_values($locators);
        $locatorDeterminants = array('//' => '|', 'css='=> ', ');
        $implodeParameter = '';
        foreach ($locatorDeterminants as $matchValue => $implodeValue) {
            $isOneType = true;
            foreach ($values as $value) {
                if (!strpos($value, $matchValue) === 0) {
                    $isOneType = false;
                    break;
                }
            }
            if ($isOneType) {
                $implodeParameter = $implodeValue;
                break;
            }
        }
        if (!$implodeParameter) {
            throw new RuntimeException('Locators must be the same type: ' . print_r($values, true));
        }
        return implode($implodeParameter, $values);
    }

    /**
     * Waits for the element to appear
     *
     * @param string|array $locator XPath locator or array of locator's
     * @param int $timeout Timeout period in seconds (by default = null)
     *
     * @throws RuntimeException
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForElement($locator, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        if (is_array($locator)) {
            $locator = self::combineLocatorsToOne($locator);
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $availableElement = $this->elementIsPresent($locator);
            if ($availableElement) {
                return $availableElement;
            }
            sleep(1);
        }
        throw new RuntimeException('Timeout after ' . $timeout . ' seconds.');
    }

    /**
     * Waits for the element(alert) to appear
     *
     * @param string|array $locator
     * @param int $timeout
     *
     * @throws RuntimeException
     * @return bool
     */
    public function waitForElementOrAlert($locator, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        if (is_array($locator)) {
            $locator = self::combineLocatorsToOne($locator);
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            if ($this->isElementPresent($locator)) {
                return true;
            }
            if ($this->isAlertPresent()) {
                return true;
            }
            sleep(1);
        }
        throw new RuntimeException('Timeout after ' . $timeout . ' seconds.');
    }

    /**
     * Waits for the element(s) to be visible
     *
     * @param string|array $locator XPath locator or array of locator's
     * @param int $timeout Timeout period in seconds (by default = null)
     *
     * @throws RuntimeException
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForElementVisible($locator, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        if (is_array($locator)) {
            $locator = self::combineLocatorsToOne($locator);
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $availableElement = $this->elementIsPresent($locator);
            if ($availableElement && $availableElement->displayed()) {
                return $availableElement;
            }
            sleep(1);
        }
        throw new RuntimeException('Timeout after ' . $timeout . ' seconds.');
    }

    /**
     * Waits for the element(s) to be visible
     *
     * @param string|array $locator XPath locator or array of locator's
     * @param int $timeout Timeout period in seconds (by default = null)
     *
     * @throws RuntimeException
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForElementEditable($locator, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        if (is_array($locator)) {
            $locator = self::combineLocatorsToOne($locator);
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $availableElement = $this->elementIsPresent($locator);
            if ($availableElement && $availableElement->enabled()) {
                return $availableElement;
            }
            sleep(1);
        }
        throw new RuntimeException('Timeout after ' . $timeout . ' seconds.');
    }

    /**
     * Waits for AJAX request to continue.<br>
     * Method works only if AJAX request was sent by Prototype or JQuery framework.
     *
     * @param int $timeout Timeout period in milliseconds. If not set, uses a default period.
     */
    public function waitForAjax($timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        $ajax = 'return Ajax.activeRequestCount;';
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $ajaxResult = $this->execute(array('script' => $ajax, 'args' => array()));
            if ($ajaxResult == 0 || $ajaxResult == null) {
                return;
            }
            sleep(1);
        }
//        if (is_null($timeout)) {
//            $timeout = $this->_browserTimeoutPeriod;
//        }
//        $jsCondition = 'var c = function(){if(typeof selenium.browserbot.getCurrentWindow().Ajax != "undefined"){'
//                       . 'if(selenium.browserbot.getCurrentWindow().Ajax.activeRequestCount){return false;};};'
//                       . 'if(typeof selenium.browserbot.getCurrentWindow().jQuery != "undefined"){'
//                       . 'if(selenium.browserbot.getCurrentWindow().jQuery.active){return false;};};return true;};c();';
//        $this->waitForCondition($jsCondition, $timeout);
    }

    /**
     * Click 'Save and continue edit' control on page with tabs
     *
     * @param string $controlType
     * @param string $controlName
     */
    public function saveAndContinueEdit($controlType, $controlName)
    {
        $tabUimap = $this->_getActiveTabUimap();
        $tabName = $tabUimap->getTabId();
        $this->addParameter('tab', $this->getControlAttribute('tab', $tabName, 'id'));
        $this->clickControlAndWaitMessage($controlType, $controlName);
        $this->waitForElementNotPresent(self::$xpathLoadingHolder);
    }

    /**
     * Submits the opened form.
     *
     * @param string $buttonName Name of the button, what intended to save (submit) form (from UIMap)
     * @param bool $validate
     *
     * @return Mage_Selenium_TestCase
     */
    public function saveForm($buttonName, $validate = true)
    {
        return $this->clickControlAndWaitMessage('button', $buttonName, $validate);
    }

    /**
     * Click control and wait message
     *
     * @param string $controlType Type of control (e.g. button|link)
     * @param string $controlName Name of a control from UIMap
     * @param bool $validate
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickControlAndWaitMessage($controlType, $controlName, $validate = true)
    {
        $messagesXpath = $this->getBasicXpathMessagesExcludeCurrent(array('success', 'error', 'validation'));
        $this->clickControl($controlType, $controlName, false);
        $this->waitForElement($messagesXpath);
        $this->addParameter('id', $this->defineIdFromUrl());
        if ($validate) {
            $this->validatePage();
        }

        return $this;
    }

    /**
     * @param string|array $types
     *
     * @return array|string
     */
    public function getBasicXpathMessagesExcludeCurrent($types)
    {
        foreach ($this->getMessagesOnPage() as $key => $value) {
            self::$_messages[$key] = array_unique($value);
        }
        if (is_string($types)) {
            $types = explode(',', $types);
            $types = array_map('trim', $types);
        }
        $returnXpath = array();
        foreach ($types as $message) {
            ${$message} = $this->_getMessageXpath('general_' . $message);
            if (array_key_exists($message, self::$_messages)) {
                $exclude = '';
                foreach (self::$_messages[$message] as $messageText) {
                    $exclude .= "[not(normalize-space(..//.)='$messageText')]";
                }
                ${$message} .= $exclude;
            }
            $returnXpath[] = ${$message};
        }
        return (count($returnXpath) == 1) ? $returnXpath[0] : $returnXpath;
    }

    /**
     * Performs scrolling to the specified element in the specified list(block) with the specified name.
     *
     * @param string $elementType Type of the element that should be visible after scrolling
     * @param string $elementName Name of the element that should be visible after scrolling
     * @param string $blockType Type of the block where to use scroll
     * @param string $blockName Name of the block where to use scroll
     */
    public function moveScrollToElement($elementType, $elementName, $blockType, $blockName)
    {
        // Getting @ID of the element what should be visible after scrolling
        $specElementId = $this->getControlAttribute($elementType, $elementName, 'id');
        // Getting @ID of the block where scroll is using
        $specFieldsetId = $this->getControlAttribute($blockType, $blockName, 'id');
        // Getting offset position of the element what should be visible after scrolling
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");
        // Moving scroll bar to previously defined offset
        // Position (to the element what should be visible after scrolling)
        $this->getEval(
            "this.browserbot.findElement('id=" . $specFieldsetId . "').scrollTop = " . $destinationOffsetTop);
    }

    /**
     * Moves the specified element (with type = $elementType and name = $elementName)<br>
     * over the specified JS tree (with type = $blockType and name = $blockName)<br>
     * to position = $moveToPosition
     *
     * @param string $elementType Type of the element to move
     * @param string $elementName Name of the element to move
     * @param string $blockType Type of the block that contains JS tree
     * @param string $blockName Name of the block that contains JS tree
     * @param integer $moveToPosition Index of the position where element should be after moving (default = 1)
     */
    public function moveElementOverTree($elementType, $elementName, $blockType, $blockName, $moveToPosition = 1)
    {
        // Getting XPath of the element to move
        $specElementXpath = $this->_getControlXpath($elementType, $elementName);
        // Getting @ID of the element to move
        $specElementId = $this->getAttribute($specElementXpath . "@id");
        // Getting XPath of the block what is a JS tree
        $specFieldsetXpath = $this->_getControlXpath($blockType, $blockName);
        // Getting @ID of the block what is a JS tree
        $specFieldsetId = $this->getAttribute($specFieldsetXpath . "@id");
        // Getting offset position of the element to move
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");
        // Storing of current height of the block with JS tree
        $tmpBlockHeight =
            (integer)$this->getEval("this.browserbot.findElement('id=" . $specFieldsetId . "').style.height");
        // If element to move situated abroad of the current height, it will be increased
        if ($destinationOffsetTop >= $tmpBlockHeight) {
            $destinationOffsetTop = $destinationOffsetTop + 50;
            $this->getEval(
                "this.browserbot.findElement('id=" . $specFieldsetId . "').style.height='" . $destinationOffsetTop
                . "px'");
        }
        $this->clickAt($specElementXpath, '1,1');
        $blockTo = $specFieldsetXpath . '//li[' . $moveToPosition . ']//a//span';
        $this->mouseDownAt($specElementXpath, '1,1');
        $this->mouseMoveAt($blockTo, '1,1');
        $this->mouseUpAt($blockTo, '1,1');
        $this->clickAt($specElementXpath, '1,1');
    }

    /**
     * Searches for the specified data in specific the grid and opens the found item.
     *
     * @param array $data Array of data to look up
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     * @param string $fieldSetName Fieldset name that contains the grid
     */
    public function searchAndOpen(array $data, $fieldSetName, $willChangePage = true)
    {
        $this->_prepareDataForSearch($data);
        $trLocator = $this->search($data, $fieldSetName);

        if ($trLocator) {
            $element = $this->getElement($trLocator . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
            if ($willChangePage) {
                $itemId = $this->defineIdFromTitle($trLocator);
                $this->addParameter('id', $itemId);
                $element->click();
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->validatePage();
            } else {
                $element->click();
                $this->waitForAjax();
            }
        } else {
            $this->fail('Can\'t find item in grid for data: ' . print_r($data, true));
        }
    }

    /**
     * Searches for the specified data in specific the grid and selects the found item.
     *
     * @param array $data Array of data to look up
     * @param string $fieldSetName Fieldset name that contains the grid
     */
    public function searchAndChoose(array $data, $fieldSetName)
    {
        $this->_prepareDataForSearch($data);
        $trLocator = $this->search($data, $fieldSetName);
        if ($trLocator) {
            $trLocator .= "//input[contains(@class,'checkbox') or contains(@class,'radio')][not(@disabled)]";
            $element = $this->getElement($trLocator);
            if (!$element->selected()) {
                $element->click();
            }
        } else {
            $this->fail('Cant\'t find item in grid for data: ' . print_r($data, true));
        }
    }

    /**
     * Prepare data array to search in grid
     *
     * @param array $data Array of data to look up
     * @param array $checkFields
     *
     * @return array
     */
    protected function _prepareDataForSearch(array &$data, array $checkFields = array('dropdown' => 'website'))
    {
        foreach ($checkFields as $fieldType => $fieldName) {
            if (array_key_exists($fieldName, $data) && !$this->controlIsPresent($fieldType, $fieldName)) {
                unset($data[$fieldName]);
            }
        }

        return $data;
    }

    /**
     * Searches the specified data in the specific grid. Returns null or XPath of the found data.
     *
     * @param array $data Array of data to look up.
     * @param string $fieldsetName Fieldset name that contains the grid
     *
     * @return string
     */
    public function search(array $data, $fieldsetName)
    {
        $fieldsetUimap = $this->_findUimapElement('fieldset', $fieldsetName);
        $fieldsetLocator = $fieldsetUimap->getXpath($this->_paramsHelper);
        $resetButtonElement = $this->getElement($this->_getControlXpath('button', 'reset_filter', $fieldsetUimap));
        $jsName = $resetButtonElement->attribute('onclick');
        $jsName = preg_replace('/\.[\D]+\(\)/', '', $jsName);
        $scriptXpath = "//script[contains(text(),\"$jsName.useAjax = ''\")]";
        $pageToLoad = $this->elementIsPresent($scriptXpath);
        $resetButtonElement->click();
        if (!$pageToLoad) {
            $this->waitForAjax();
        } else {
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage();
        }
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');

        //Forming xpath that contains string 'Total $number records found' where $number - number of items in table
        list(, , $totalCount) = explode('|', $this->getElement($fieldsetLocator . "//td[@class='pager']")->text());
        $totalCount = trim(preg_replace('/[A-Za-z]+/', '', $totalCount));
        $pagerLocator = $fieldsetLocator . $qtyElementsInTable . "[not(text()='" . $totalCount . "')]";

        $trLocator = $this->formSearchXpath($data);

        $element = $this->elementIsPresent($fieldsetLocator . $trLocator);
        if (!$element && $totalCount > 20) {
            // Fill in search form and click 'Search' button
            $this->fillFieldset($data, $fieldsetName);
            $this->getElement($this->_getControlXpath('button', 'search', $fieldsetUimap))->click();
            $this->waitForElement($pagerLocator);
            $element = $this->elementIsPresent($fieldsetLocator . $trLocator);
        }
        return ($element) ? $fieldsetLocator . $trLocator : null;
    }


    /**
     * Forming xpath that contains the data to look up
     *
     * @param array $data Array of data to look up
     *
     * @return string
     */
    public function formSearchXpath(array $data)
    {
        $xpathTR = "//table[@class='data']//tr";
        foreach ($data as $key => $value) {
            if (!preg_match('/_from/', $key) && !preg_match('/_to/', $key) && !is_array($value)) {
                if (strpos($value, "'")) {
                    $value = "concat('" . str_replace('\'', "',\"'\",'", $value) . "')";
                } else {
                    $value = "'" . $value . "'";
                }
                $xpathTR .= "[td[contains(text(),$value)]]";
            }
        }
        return $xpathTR;
    }

    /**
     * Fill fieldset
     *
     * @param array $data
     * @param string $fieldsetId
     * @param bool $failIfFieldsWithoutXpath
     *
     * @return bool
     */
    public function fillFieldset(array $data, $fieldsetId, $failIfFieldsWithoutXpath = true)
    {
        $this->assertNotEmpty($data);
        $fillData = $this->getDataMapForFill($data, 'fieldset', $fieldsetId);

        if (!isset($fillData['isPresent']) && !$failIfFieldsWithoutXpath) {
            return false;
        }

        if (isset($fillData['isNotPresent']) && $failIfFieldsWithoutXpath) {
            $message =
                "\n" . 'Current page "' . $this->getCurrentPage() . '": ' . 'There are no fields in "' . $fieldsetId
                . '" fieldset:' . "\n" . implode("\n", array_keys($fillData['isNotPresent']));
            $this->fail($message);
        }

        foreach ($fillData['isPresent'] as $fieldData) {
            $this->_fill($fieldData);
        }
        return true;
    }

    /**
     * Fill tab
     *
     * @param array $data
     * @param string $tabId
     * @param bool $failIfFieldsWithoutXpath
     *
     * @return bool
     */
    public function fillTab(array $data, $tabId, $failIfFieldsWithoutXpath = true)
    {
        $this->assertNotEmpty($data);
        $fillData = $this->getDataMapForFill($data, 'tab', $tabId);

        if (!isset($fillData['isPresent']) && !$failIfFieldsWithoutXpath) {
            return false;
        }

        if (isset($fillData['isNotPresent']) && $failIfFieldsWithoutXpath) {
            $message = "\n" . 'Current page "' . $this->getCurrentPage() . '": ' . 'There are no fields in "' . $tabId
                       . '" tab:' . "\n" . implode("\n", array_keys($fillData['isNotPresent']));
            $this->fail($message);
        }

        $this->openTab($tabId);
        foreach ($fillData['isPresent'] as $fieldData) {
            $this->_fill($fieldData);
        }
        return true;
    }

    /**
     * Fills any form with the provided data. Specific Tab can be filled only if $tabId is provided.
     *
     * @param array|string $data Array of data to fill or datasource name
     * @param string $tabId Tab ID from UIMap (by default = '')
     *
     * @throws OutOfRangeException|PHPUnit_Framework_Exception
     * @deprecated
     * @see fillTab() or fillFieldset()
     */
    public function fillForm($data, $tabId = '')
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

        $formData = $this->getCurrentUimapPage()->getMainForm();
        if ($tabId && $formData->getTab($tabId)) {
            $fieldsets = $formData->getTab($tabId)->getAllFieldsets($this->_paramsHelper);
        } else {
            $fieldsets = $formData->getAllFieldsets($this->_paramsHelper);
        }
        // if we have got empty UIMap but not empty dataset
        if (empty($fieldsets)) {
            throw new OutOfRangeException(
                "Can't find main form in UIMap array for page '" . $this->getCurrentPage() . "', area['"
                . $this->_configHelper->getArea() . "']");
        }

        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        if ($tabId) {
            $this->openTab($tabId);
        }

        try {
            foreach ($formDataMap as $formFieldName => $formField) {
                switch ($formField['type']) {
                    case self::FIELD_TYPE_INPUT:
                        $this->fillField($formFieldName, $formField['value'], $formField['path']);
                        break;
                    case self::FIELD_TYPE_CHECKBOX:
                        $this->fillCheckbox($formFieldName, $formField['value'], $formField['path']);
                        break;
                    case self::FIELD_TYPE_DROPDOWN:
                        $this->fillDropdown($formFieldName, $formField['value'], $formField['path']);
                        break;
                    case self::FIELD_TYPE_RADIOBUTTON:
                        $this->fillRadiobutton($formFieldName, $formField['value'], $formField['path']);
                        break;
                    case self::FIELD_TYPE_MULTISELECT:
                        $this->fillMultiselect($formFieldName, $formField['value'], $formField['path']);
                        break;
                    default:
                        throw new PHPUnit_Framework_Exception('Unsupported field type');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $errorMessage = isset($formFieldName) ? 'Problem with field \'' . $formFieldName . '\': ' . $e->getMessage()
                : $e->getMessage();
            $this->fail($errorMessage);
        }
    }

    /**
     * Verifies values on the opened form
     *
     * @param array|string $data Array of data to verify or datasource name
     * @param string $tabId Defines a specific Tab on the page that contains the form to verify (by default = '')
     * @param array $skipElements Array of elements that will be skipped during verification <br>
     * (default = array('password'))
     *
     * @throws OutOfRangeException
     * @throws RuntimeException
     * @return bool
     */
    public function verifyForm($data, $tabId = '', $skipElements = array('password', 'password_confirmation'))
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

        $formData = $this->getCurrentUimapPage()->getMainForm();
        if ($tabId && $formData->getTab($tabId)) {
            $fieldsets = $formData->getTab($tabId)->getAllFieldsets($this->_paramsHelper);
        } else {
            $fieldsets = $formData->getAllFieldsets($this->_paramsHelper);
        }
        // if we have got empty UIMap but not empty dataset
        if (empty($fieldsets)) {
            throw new OutOfRangeException(
                "Can't find main form in UIMap array for page '" . $this->getCurrentPage() . "', area['"
                . $this->_configHelper->getArea() . "']");
        }

        if ($tabId) {
            $this->openTab($tabId);
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $skipElements) || $value === '%noValue%') {
                unset($data[$key]);
            }
        }
        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        $resultFlag = true;
        foreach ($formDataMap as $formFieldName => $formField) {
            $availableElement = $this->elementIsPresent($formField['path']);
            switch ($formField['type']) {
                case self::FIELD_TYPE_INPUT:
                    if ($availableElement) {
                        $actualValue = $availableElement->value();
                        if ($actualValue != $formField['value']) {
                            $this->addVerificationMessage(
                                $formFieldName . ": The stored value is not equal to specified: ('"
                                . $formField['value'] . "' != '" . $actualValue . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_CHECKBOX:
                case self::FIELD_TYPE_RADIOBUTTON:
                    if ($availableElement) {
                        $isChecked = $this->isChecked($formField['path']);
                        $expectedVal = strtolower($formField['value']);
                        if (($isChecked && $expectedVal != 'yes')
                            || (!$isChecked && !($expectedVal == 'no' || $expectedVal == ''))
                        ) {
                            $printVal = ($isChecked) ? 'yes' : 'no';
                            $this->addVerificationMessage(
                                $formFieldName . ": The stored value is not equal to specified: ('" . $expectedVal
                                . "' != '" . $printVal . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_DROPDOWN:
                    if ($availableElement) {
                        $actualValue =
                            $availableElement->element($this->using('xpath')->value("//option[@selected]"))->text();
                        if ($actualValue != $formField['value']) {
                            $this->addVerificationMessage(
                                $formFieldName . ": The stored value is not equal to specified: ('"
                                . $formField['value'] . "' != '" . $actualValue . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_MULTISELECT:
                    if ($availableElement) {
                        $selectedLabels = array();
                        try {
                            $selectedLabels = $this->getSelectedLabels($formField['path']);
                            foreach ($selectedLabels as $key => $label) {
                                $selectedLabels[$key] = trim($label, chr(0xC2) . chr(0xA0));
                            }
                        } catch (RuntimeException $e) {
                            if (strpos($e->getMessage(), 'No option selected') === false) {
                                throw $e;
                            }
                        }
                        $expectedLabels = explode(',', $formField['value']);
                        $expectedLabels = array_map('trim', $expectedLabels);
                        $expectedLabels = array_diff($expectedLabels, array(''));
                        foreach ($expectedLabels as $value) {
                            if (!in_array($value, $selectedLabels)) {
                                $this->addVerificationMessage($formFieldName . ": The value '" . $value
                                                              . "' is not selected. (Selected values are: '"
                                                              . implode(', ', $selectedLabels) . "')");
                                $resultFlag = false;
                            }
                        }
                        if (count($selectedLabels) != count($expectedLabels)) {
                            $this->addVerificationMessage(
                                "Amounts of the expected options are not equal to selected: ('" . $formField['value']
                                . "' != '" . implode(', ', $selectedLabels) . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_PAGEELEMENT:
                    if ($availableElement) {
                        $actualValue = trim($this->getText($formField['path']));
                        if ($actualValue != $formField['value']) {
                            $this->addVerificationMessage(
                                $formFieldName . ": The stored value is not equal to specified: ('"
                                . $formField['value'] . "' != '" . $actualValue . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find pageelement (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                default:
                    $this->addVerificationMessage('Unsupported field type');
                    $resultFlag = false;
            }
        }

        return $resultFlag;
    }

    /**
     * Fill any type of field(dropdown|field|checkbox|multiselect|radiobutton)
     *
     * @param $fieldData
     *
     * @throws OutOfRangeException
     */
    protected function _fill($fieldData)
    {
        switch ($fieldData['type']) {
            case self::FIELD_TYPE_INPUT:
                $this->fillField($fieldData['name'], $fieldData['value'], $fieldData['locator']);
                break;
            case self::FIELD_TYPE_CHECKBOX:
                $this->fillCheckbox($fieldData['name'], $fieldData['value'], $fieldData['locator']);
                break;
            case self::FIELD_TYPE_RADIOBUTTON:
                $this->fillRadiobutton($fieldData['name'], $fieldData['value'], $fieldData['locator']);
                break;
            case self::FIELD_TYPE_MULTISELECT:
                $this->fillMultiselect($fieldData['name'], $fieldData['value'], $fieldData['locator']);
                break;
            case self::FIELD_TYPE_DROPDOWN:
                $this->fillDropdown($fieldData['name'], $fieldData['value'], $fieldData['locator']);
                break;
            default:
                throw new OutOfRangeException(
                    'Unsupported field type: "' . $fieldData['type'] . '" for fillFieldset() function');
        }
    }

    /**
     * Fills a text field of control type by typing a value.
     *
     * @param string $name
     * @param string $value
     * @param string|null $locator
     *
     * @throws RuntimeException
     */
    protected function fillField($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath('field', $name);
        }
        $element = $this->waitForElementEditable($locator);
        $element->clear();
        $element->value($value);
        //@TODO
        //$this->waitForAjax();
    }

    /**
     * Fills 'multiselect' control by selecting the specified values.
     *
     * @param string $name
     * @param string $value
     * @param string|null $xpath
     *
     * @throws RuntimeException
     */
    protected function fillMultiselect($name, $value, $xpath = null)
    {
        if (is_null($xpath)) {
            $xpath = $this->_getControlXpath('multiselect', $name);
        }
        $this->waitForElementEditable($xpath);
        $this->removeAllSelections($xpath);
        if (strtolower($value) == 'all') {
            $options = $this->getSelectOptions($xpath);
            foreach ($options as $key => $opt) {
                $options[$key] = trim($opt, chr(0xC2) . chr(0xA0));
            }
        } else {
            $options = explode(',', $value);
            $options = array_map('trim', $options);
        }
        foreach ($options as $option) {
            if ($option) {
                if ($this->isElementPresent($xpath . "//option[text()='" . $option . "']")) {
                    $this->addSelection($xpath, 'label=' . $option);
                } else {
                    $this->addSelection($xpath, 'regexp:' . preg_quote($option));
                }
            }
        }
    }

    /**
     * Fills the 'dropdown' control by selecting the specified value.
     *
     * @param string $name
     * @param string $value
     * @param string|null $locator
     *
     * @throws RuntimeException
     */
    protected function fillDropdown($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath('dropdown', $name);
        }
        $element = $this->waitForElementEditable($locator);
        $defaultValue = $element->element($this->using('xpath')->value("//option[@selected]"))->text();
        if ($defaultValue != $value) {
            $element->value($value);
            //@TODO
            //$this->waitForAjax();
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @param string|null $xpath
     *
     * @throws RuntimeException
     */
    protected function fillCheckbox($name, $value, $xpath = null)
    {
        if (is_null($xpath)) {
            $xpath = $this->_getControlXpath('checkbox', $name);
        }
        $currentValue = $this->getValue($xpath);
        if (strtolower($value) == 'yes') {
            if ($currentValue == 'off' || $currentValue == '0') {
                $this->click($xpath);
                $this->waitForElementEditable($xpath);
            }
        } elseif (strtolower($value) == 'no') {
            if ($currentValue == 'on' || $currentValue == '1') {
                $this->click($xpath);
                $this->waitForElementEditable($xpath);
            }
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @param string|null $xpath
     *
     * @throws RuntimeException
     */
    protected function fillRadiobutton($name, $value, $xpath = null)
    {
        if (is_null($xpath)) {
            $xpath = $this->_getControlXpath('radiobutton', $name);
        }
        if (strtolower($value) == 'yes') {
            $this->click($xpath);
            $this->waitForElementEditable($xpath);
        } elseif (strtolower($value) == 'no') {
            $this->uncheck($xpath);
            $this->waitForElementEditable($xpath);
        }
    }

    ################################################################################
    #                                                                              #
    #                             Magento helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Waits for "Please wait" animated gif to appear and disappear.
     *
     * @param integer $waitAppear Timeout in seconds to wait for the loader to appear (by default = 10)
     * @param integer $waitDisappear Timeout in seconds to wait for the loader to disappear (by default = 30)
     *
     * @return Mage_Selenium_TestCase
     */
    public function pleaseWait($waitAppear = 10, $waitDisappear = 30)
    {
        //$this->waitForElementPresent(self::$xpathLoadingHolder, $waitAppear * 1000);
        $this->waitForAjax();
        $this->waitForElementNotPresent(self::$xpathLoadingHolder, $waitDisappear * 1000);

        return $this;
    }

    /**
     * Logs in as a default admin user on back-end
     * @return Mage_Selenium_TestCase
     */
    public function loginAdminUser()
    {
        $this->admin('log_in_to_admin', false);
        $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                           'password'  => $this->_configHelper->getDefaultPassword());
        if ($this->_findCurrentPageFromUrl() != $this->_firstPageAfterAdminLogin) {
            $this->validatePage('log_in_to_admin');
            $this->fillFieldset($loginData, 'log_in');
            $this->clickButton('login', false);
            $this->waitForElement(array($this->_getControlXpath('pageelement', 'admin_logo'),
                                        $this->_getMessageXpath('general_error'),
                                        $this->_getMessageXpath('general_validation')));
            if ($this->controlIsPresent('link', 'go_to_notifications')) {
                $this->waitForElement($this->_getControlXpath('button', 'close'), 5)->click();
            }
        }
        $this->validatePage($this->_firstPageAfterAdminLogin);
        return $this;
    }

    /**
     * Logs out from back-end
     * @return Mage_Selenium_TestCase
     */
    public function logoutAdminUser()
    {
        $logOutLocator = $this->_getControlXpath('link', 'log_out');
        $availableElement = $this->elementIsPresent($logOutLocator);
        if ($availableElement) {
            $availableElement->click();
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        $this->validatePage('log_in_to_admin');

        return $this;
    }

    /**
     * Flush Cache Storage
     */
    public function flushCache()
    {
        $this->admin('cache_storage_management');
        $this->clickButtonAndConfirm('flush_cache_storage', 'flush_cache_confirmation', false);
        $this->waitForNewPage();
        $this->validatePage('cache_storage_management');
        $this->assertMessagePresent('success');
    }

    /**
     * Clears invalided cache in Admin
     */
    public function clearInvalidedCache()
    {
        $xpath = $this->_getControlXpath('link', 'invalided_cache');
        if ($this->isElementPresent($xpath)) {
            $this->clickAndWait($xpath);
            $this->validatePage('cache_storage_management');

            $invalided = array('cache_disabled', 'cache_invalided');
            foreach ($invalided as $value) {
                $xpath = $this->_getControlXpath('pageelement', $value);
                $qty = $this->getXpathCount($xpath);
                for ($i = 1; $i < $qty + 1; $i++) {
                    $this->fillCheckbox('', 'Yes', $xpath . '[' . $i . ']//input');
                }
            }
            $this->fillFieldset(array('cache_action' => 'Refresh'), 'cache_grid');

            $selectedItems = $this->getText($this->_getControlXpath('pageelement', 'selected_items'));
            $this->addParameter('qtySelected', $selectedItems);

            $this->clickButton('submit', false);
            $alert = $this->isAlertPresent();
            if ($alert) {
                $text = $this->getAlert();
                $this->fail($text);
            }
            $this->waitForNewPage();
            $this->validatePage('cache_storage_management');
        }
    }

    /**
     * Reindex indexes that are marked as 'reindex required' or 'update required'.
     */
    public function reindexInvalidedData()
    {
        $xpath = $this->_getControlXpath('link', 'invalided_index');
        if ($this->isElementPresent($xpath)) {
            $this->clickAndWait($xpath);
            $this->validatePage('index_management');

            $invalided = array('reindex_required', 'update_required');
            foreach ($invalided as $value) {
                $xpath = $this->_getControlXpath('pageelement', $value);
                while ($this->isElementPresent($xpath)) {
                    $this->click($xpath . "//a[text()='Reindex Data']");
                    $this->waitForNewPage();
                    $this->validatePage('index_management');
                }
            }
        }
    }

    /**
     *  Reindex All Data
     */
    public function reindexAllData()
    {
        $this->admin('index_management');
        $cellId = $this->getColumnIdByName('Index');
        $xpath = $this->_getControlXpath('pageelement', 'index_line');
        $qtyIndexes = $this->getXpathCount($xpath);
        for ($i = 1; $i <= $qtyIndexes; $i++) {
            $name = trim($this->getText($xpath . "[$i]/td[$cellId]"));
            $this->addParameter('indexName', $name);
            $this->clickControl('link', 'reindex_index', false);
            $this->waitForNewPage();
            $this->validatePage('index_management');
            $this->assertMessagePresent('success', 'success_reindex');
        }
    }

    /**
     * @throws RuntimeException
     */
    public function waitForNewPage()
    {
        $notLoaded = true;
        $retries = 0;
        while ($notLoaded) {
            try {
                $retries++;
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $notLoaded = false;
            } catch (RuntimeException $e) {
                if ($this->isElementPresent("//*[@id='errorPageContainer']")) {
                    $error = 'Problem loading page. ';
                    if ($this->isElementPresent("//*[@id='errorTitleText']")) {
                        $error .= $this->getText("//*[@id='errorTitleText']") . '. ';
                    }
                    if ($this->isElementPresent("//*[@id='errorShortDescText']")) {
                        $error .= $this->getText("//*[@id='errorShortDescText']");
                    }
                    throw new RuntimeException($error);
                }
                if ($retries == 10) {
                    throw new RuntimeException('Timed out after ' . ($this->_browserTimeoutPeriod * 10) . 'ms');
                }
            }
        }
    }

    /**
     * Performs LogOut customer on front-end
     * @return Mage_Selenium_TestCase
     */
    public function logoutCustomer()
    {
        if ($this->getArea() !== 'frontend') {
            $this->frontend();
        }
        if ($this->controlIsPresent('link', 'log_out')) {
            $this->clickControl('link', 'log_out', false);
            $this->waitForTextPresent('You are now logged out');
            $this->waitForTextNotPresent('You are now logged out');
            $this->deleteAllVisibleCookies();
            $this->validatePage('home_page');
        }

        return $this;
    }

    /**
     * Selects StoreView on Frontend
     *
     * @param string $storeViewName
     */
    public function selectFrontStoreView($storeViewName = 'Default Store View')
    {
        $dropdown = ($this->controlIsPresent('dropdown', 'your_language')) ? $this->_getControlXpath('dropdown',
            'your_language') : false;
        if ($dropdown != false) {
            $toSelect = $dropdown . '//option[normalize-space(text())="' . $storeViewName . '"]';
            $isSelected = $toSelect . '[@selected]';
            if (!$this->isElementPresent($isSelected)) {
                $this->select($dropdown, $storeViewName);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            }
            $this->assertElementPresent($isSelected, '\'' . $storeViewName . '\' store view not selected');
        } else {
            $this->addParameter('storeView', $storeViewName);
            $isSelected = $this->_getControlXpath('pageelement', 'selected_store_view');
            $storeViewXpath = $this->_getControlXpath('link', 'your_language');
            if (!$this->controlIsPresent('pageelement', 'selected_store_view')) {
                $this->clickControl('pageelement', 'change_store_view', false);
                if ($this->waitForElementVisible($storeViewXpath, $this->_browserTimeoutPeriod)) {
                    $this->clickControl('link', 'your_language', false);
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                } else {
                    $this->fail('Store view cannot be changed to ' . $storeViewName);
                }
            }
            $this->assertElementPresent($isSelected, '\'' . $storeViewName . '\' store view not selected');
        }
    }

    /**
     * @TODO
     *
     * @param string $controlType
     * @param string $controlName
     * @param null|string $scopePath
     * @param string $scopeName
     *
     * @return bool
     */
    public function selectStoreScope($controlType, $controlName, $scopePath = null, $scopeName = 'storeView')
    {
        if (is_null($scopePath)) {
            $scopePath = 'Main Website/Main Website Store/Default Store View';
        }
        //Define scope parameters
        $scopePath = explode('/', $scopePath);
        $storeView = array_pop($scopePath);
        $website = $store = '';
        if (!empty($scopePath)) {
            if (count($scopePath) == 2) {
                list($website, $store) = $scopePath;
            } elseif (count($scopePath) == 1) {
                list($store) = $scopePath;
            }
        }
        $method = 'fill' . ucfirst(strtolower($controlType));
        $xpath = $this->_getControlXpath($controlType, $controlName);
        //Select value if only Store View name specified
        if (!$website && !$store) {
            if (!$this->isElementPresent($xpath . "//option[normalize-space(text())='$storeView'][@selected]")) {
                $this->$method($controlName, $storeView);
                return true;
            }
            return false;
        }

        if ($website && $store) {
            $isSelectWebsite = false;
            if ($this->getXpathCount($xpath . '/option') > 1) {
                $isSelectWebsite = true;
            }
            if ($isSelectWebsite) {
                $websiteXpath = $xpath . "/*[normalize-space(text())='$website']";
                $storeXpath = $websiteXpath . "/following-sibling::*[normalize-space(@label)='$store']";
                $storeViewXpath = $storeXpath . "/option[normalize-space(text())='$storeView']";
            } else {
                $websiteXpath = $xpath . "/*[normalize-space(@label)='$website']";
                $storeXpath = $websiteXpath . "/following-sibling::*[contains(@label, '$store')]";
                $storeViewXpath = $storeXpath . "/option[contains(text(),'$storeView')]";
            }
            $value = $this->getAttribute($storeViewXpath . '@value');
            if (!$this->isElementPresent($xpath . "//option[@value='$value'][@selected]")) {
                $this->$method($controlName, $value);
                return true;
            }
            return false;
        }
        return false;
    }

    ################################################################################
    #                                                                              #
    #       Should be removed when onNotSuccessfulTest is fixed                    #
    #                                                                              #
    ################################################################################
    /**
     * @param Exception $e
     *
     * @throws Exception|RuntimeException
     */
    public function onNotSuccessfulTest(Exception $e)
    {
        //if (!$this->frameworkConfig['shareSession']) {
        //    $this->drivers[0]->stopBrowserSession();
        //}

        throw $e;
    }

    ################################################################################
    #                                                                              #
    #                               SELENIUM 2 FUNCTIONS                           #
    #                                                                              #
    ################################################################################
    /**
     * @param $locator
     *
     * @return string
     */
    public function getLocatorStrategy($locator)
    {
        $locatorType = 'xpath';
        if (preg_match('/^css=/', $locator)) {
            $locatorType = 'css locator';
        }
        return $locatorType;
    }

    /**
     * @param string $locator
     * @param bool $failIfEmpty
     *
     * @return array
     */
    public function getElements($locator, $failIfEmpty = true)
    {
        $locatorType = $this->getLocatorStrategy($locator);
        $elements = $this->elements($this->using($locatorType)->value($locator));
        if (empty($elements) && $failIfEmpty) {
            $this->fail('Element(s) with locator: "' . $locator . '" is not found on page');
        }
        return $elements;
    }

    /**
     * @param string $locator
     *
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getElement($locator)
    {
        $elements = $this->getElements($locator);
        return array_shift($elements);
    }

    /**
     * @param string $locator
     *
     * @return bool|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function elementIsPresent($locator)
    {
        $elements = $this->getElements($locator, false);
        return empty($elements) ? false : array_shift($elements);
    }

    /**
     * @param string $locator
     * @param string $getCommand attribute|displayed|enabled|name|selected|size|text|value|location
     * @param null|string $getParameter
     *
     * @return array
     */
    public function getElementsValue($locator, $getCommand, $getParameter = null)
    {
        $elementsValue = array();
        $elements = $this->getElements($locator, false);
        if (empty($elements)) {
            return $elementsValue;
        }
        foreach ($elements as $element) {
            $elementsValue[] = $element->$getCommand($getParameter);
        }
        return $elementsValue;
    }

    /**
     * Waits for page to load
     *
     * @param null|integer $timeout
     *
     * @return bool
     * @throws RuntimeException
     */
    public function waitForPageToLoad($timeout = NULL)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        $jsCondition = "return document['readyState']";
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $result = $this->execute(array('script' => $jsCondition, 'args' => array()));
            if ($result === 'complete') {
                return true;
            }
            sleep(1);
        }
        throw new RuntimeException('Time is out for waitForPageToLoad');
    }
}
