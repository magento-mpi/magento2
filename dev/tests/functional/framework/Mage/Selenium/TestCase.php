<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

//@codingStandardsIgnoreStart
/**
 * An extended test case implementation that adds useful helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Core_Mage_AdminUser_Helper                                                                  adminUserHelper()
 * @method Core_Mage_AdvancedSearch_Helper                                                             advancedSearchHelper()
 * @method Core_Mage_AttributeSet_Helper                                                               attributeSetHelper()
 * @method Core_Mage_Category_Helper|Enterprise_Mage_Category_Helper                                   categoryHelper()
 * @method Core_Mage_CheckoutMultipleAddresses_Helper|Enterprise_Mage_CheckoutMultipleAddresses_Helper checkoutMultipleAddressesHelper()
 * @method Core_Mage_CheckoutOnePage_Helper|Enterprise_Mage_CheckoutOnePage_Helper                     checkoutOnePageHelper()
 * @method Core_Mage_CmsPages_Helper                                                                   cmsPagesHelper()
 * @method Core_Mage_CmsStaticBlocks_Helper                                                            cmsStaticBlocksHelper()
 * @method Core_Mage_CmsWidgets_Helper|Enterprise_Mage_CmsWidgets_Helper                               cmsWidgetsHelper()
 * @method Core_Mage_CompareProducts_Helper                                                            compareProductsHelper()
 * @method Core_Mage_Csv_Helper                                                                        csvHelper()
 * @method Core_Mage_CustomerGroups_Helper                                                             customerGroupsHelper()
 * @method Core_Mage_Customer_Helper|Enterprise_Mage_Customer_Helper                                   customerHelper()
 * @method Core_Mage_DesignEditor_Helper                                                               designEditorHelper()
 * @method Core_Mage_AdminGlobalSearch_Helper                                                          adminGlobalSearchHelper()
 * @method Core_Mage_Grid_Helper                                                                       gridHelper()
 * @method Core_Mage_ImportExport_Helper|Enterprise_Mage_ImportExport_Helper                           importExportHelper()
 * @method Core_Mage_Installation_Helper                                                               installationHelper()
 * @method Core_Mage_LayeredNavigation_Helper                                                          layeredNavigationHelper()
 * @method Core_Mage_Newsletter_Helper                                                                 newsletterHelper()
 * @method Core_Mage_OrderCreditMemo_Helper                                                            orderCreditMemoHelper()
 * @method Core_Mage_OrderInvoice_Helper                                                               orderInvoiceHelper()
 * @method Core_Mage_OrderShipment_Helper                                                              orderShipmentHelper()
 * @method Core_Mage_Order_Helper|Enterprise_Mage_Order_Helper                                         orderHelper()
 * @method Core_Mage_Paypal_Helper                                                                     paypalHelper()
 * @method Core_Mage_PriceRules_Helper|Enterprise_Mage_PriceRules_Helper                               priceRulesHelper()
 * @method Core_Mage_ProductAttribute_Helper                                                           productAttributeHelper()
 * @method Core_Mage_Product_Helper|Enterprise_Mage_Product_Helper                                     productHelper()
 * @method Core_Mage_Rating_Helper                                                                     ratingHelper()
 * @method Core_Mage_Reports_Helper                                                                    reportsHelper()
 * @method Core_Mage_Review_Helper                                                                     reviewHelper()
 * @method Core_Mage_RssFeeds_Helper                                                                   rssFeedsHelper()
 * @method Core_Mage_ShoppingCart_Helper|Enterprise_Mage_ShoppingCart_Helper                           shoppingCartHelper()
 * @method Core_Mage_Store_Helper                                                                      storeHelper()
 * @method Core_Mage_SystemConfiguration_Helper                                                        systemConfigurationHelper()
 * @method Core_Mage_Tags_Helper                                                                       tagsHelper()
 * @method Core_Mage_Tax_Helper                                                                        taxHelper()
 * @method Core_Mage_Tax_TaxRule_Helper                                                                taxRuleHelper()
 * @method Core_Mage_TermsAndConditions_Helper                                                         termsAndConditionsHelper()
 * @method Core_Mage_Theme_Helper                                                                      themeHelper()
 * @method Core_Mage_TransactionalEmails_Helper                                                        transactionalEmailsHelper()
 * @method Core_Mage_UrlRewrite_Helper                                                                 urlRewriteHelper()
 * @method Core_Mage_Vde_Helper                                                                        vdeHelper()
 * @method Core_Mage_Wishlist_Helper|Enterprise_Mage_Wishlist_Helper                                   wishlistHelper()
 * @method Core_Mage_XmlSitemap_Helper                                                                 xmlSitemapHelper()
 * @method Enterprise_Mage_AddBySku_Helper                                                             addBySkuHelper()
 * @method Enterprise_Mage_Attributes_Helper                                                           attributesHelper()
 * @method Enterprise_Mage_CacheStorageManagement_Helper                                               cacheStorageManagementHelper()
 * @method Enterprise_Mage_CmsBanners_Helper                                                           cmsBannersHelper()
 * @method Enterprise_Mage_CustomerSegment_Helper                                                      customerSegmentHelper()
 * @method Enterprise_Mage_GiftRegistry_Helper                                                         giftRegistryHelper()
 * @method Enterprise_Mage_GiftWrapping_Helper                                                         giftWrappingHelper()
 * @method Enterprise_Mage_ImportExportScheduled_Helper                                                importExportScheduledHelper()
 * @method Enterprise_Mage_Invitation_Helper                                                           invitationHelper()
 * @method Enterprise_Mage_Rma_Helper                                                                  rmaHelper()
 * @method Enterprise_Mage_Rollback_Helper                                                             rollbackHelper()
 * @method Enterprise_Mage_WebsiteRestrictions_Helper                                                  websiteRestrictionsHelper()
 */
//@codingStandardsIgnoreEnd
class Mage_Selenium_TestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    ################################################################################
    #              Framework variables and constant                                #
    ################################################################################
    /**
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration
     */
    private $_testConfig;

    /**
     * Config helper instance
     * @var Mage_Selenium_Helper_Config
     */
    private $_configHelper;

    /**
     * UIMap helper instance
     * @var Mage_Selenium_Helper_Uimap
     */
    private $_uimapHelper;

    /**
     * Data helper instance
     * @var Mage_Selenium_Helper_Data
     */
    private $_dataHelper;

    /**
     * Params helper instance
     * @var Mage_Selenium_Helper_Params
     */
    private $_paramsHelper;

    /**
     * Data Generator helper instance
     * @var Mage_Selenium_Helper_DataGenerator
     */
    private $_dataGeneratorHelper;

    /**
     * Framework setting
     * @var array
     */
    public $frameworkConfig;

    /**
     * Saves HTML content of the current page if the test failed
     * @var bool
     */
    protected $_saveHtmlOnFailure = false;

    /**
     * @var bool
     */
    protected $_saveScreenOnFail = false;

    /**
     * @var null
     */
    protected $_screenshotPath = null;

    /**
     * Timeout in seconds
     * @var int
     */
    protected $_browserTimeout = 45;

    /**
     * Name of the first page after logging into the back-end
     * @var string
     */
    public $pageAfterAdminLogin;

    /**
     * Array of messages on page
     * @var array
     */
    protected $_messages = array();

    /**
     * Name of run Test Class
     * @var null
     */
    public static $testClass = null;

    /**
     * Name of last testcase in test class
     * @var array
     */
    protected static $_lastTestNameInClass = null;

    /**
     * Array of Test Helper instances
     * @var array
     */
    protected static $_testHelpers = array();

    /**
     * Additional prefix for navigation URL
     * @var string
     */
    protected $_urlPrefix = array();

    /**
     * @var array
     */
    public static $browsers = array();

    /**
     * Additional params for navigation URL
     * @var string
     */
    protected $_urlPostfix;

    /**
     * Types of uimap elements
     */
    const FIELD_TYPE_COMPOSITE_MULTISELECT = 'composite_multiselect';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_DROPDOWN = 'dropdown';
    const FIELD_TYPE_INPUT = 'field';
    const FIELD_TYPE_LINK = 'link';
    const FIELD_TYPE_MULTISELECT = 'multiselect';
    const FIELD_TYPE_PAGEELEMENT = 'pageelement';
    const FIELD_TYPE_RADIOBUTTON = 'radiobutton';

    const UIMAP_TYPE_FIELDSET = 'fieldset';
    const UIMAP_TYPE_MESSAGE = 'message';
    const UIMAP_TYPE_TAB = 'tab';
    /**
     * Message types
     */
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_VALIDATION = 'validation';

    ################################################################################
    #                             Else variables                                   #
    ################################################################################

    /**
     * Constructs a test case with the given name and browser to test execution
     *
     * @param  string $name Test case name(by default = null)
     * @param  array $data Test case data array(by default = array())
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
        $this->_saveScreenOnFail = $this->frameworkConfig['saveScreenshotOnFailure'];
        $this->_saveHtmlOnFailure = $this->frameworkConfig['saveHtmlPageOnFailure'];
        $this->_browserTimeout = (isset($this->frameworkConfig['browserTimeoutPeriod']))
            ? $this->frameworkConfig['browserTimeoutPeriod']
            : $this->_browserTimeout;
        $this->pageAfterAdminLogin = $this->_configHelper->getAfterLoginPage();
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
        try {
            return parent::__call($command, $arguments);
        } catch (PHPUnit_Extensions_Selenium2TestCase_NoSeleniumException $e) {
            $this->markTestIncomplete($e->getMessage());
        }
    }

    /**
     * Implementation of setUpBeforeClass() method in the object context, called as setUpBeforeTests()<br>
     * Used ONLY one time before execution of each class (tests in test class)
     * @throws Exception
     */
    private function setUpBeforeTestClass()
    {
        $currentTestClass = get_class($this);
        static $beforeTestsError = null;
        if (self::$testClass != $currentTestClass) {
            self::$testClass = $currentTestClass;
            $this->setLastTestNameInClass();
            try {
                $beforeTestsError = null;
                $this->setUpBeforeTests();
            } catch (Exception $e) {
                $beforeTestsError =
                    "\nError in setUpBeforeTests method for '" . $currentTestClass . "' class:\n" . $e->getMessage();
            }
            if (isset($e)) {
                throw $e;
            }
        }
        if ($beforeTestsError !== null) {
            $this->markTestSkipped($beforeTestsError);
        }
    }

    /**
     * Prepare browser session
     */
    public function prepareBrowserSession()
    {
        if (empty(self::$browsers)) {
            $browsers = $this->_configHelper->getConfigBrowsers();
            $this->setupSpecificBrowser($browsers['default']);
        }
        $this->setBrowserUrl($this->_configHelper->getBaseUrl());
        $this->prepareSession();
    }

    public final function setUp()
    {
        $this->prepareBrowserSession();
        $this->currentWindow()->maximize();
        $this->cookie()->clear();
        $this->refresh();
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
        $class = new ReflectionClass(self::$testClass);
        /** @var ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                $testMethods[] = $method->getName();
            }
        }
        $testName = end($testMethods);
        $data = PHPUnit_Util_Test::getProvidedData(self::$testClass, $testName);
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
    public final function tearDown()
    {
        if ($this->hasFailed()) {
            if ($this->_saveHtmlOnFailure) {
                $this->saveHtmlPage();
            }
            if ($this->_saveScreenOnFail) {
                $this->takeScreenshot();
            }
        } elseif (is_null($this->getExpectedException())) {
            $this->assertEmptyVerificationErrors();
        } else {
            $this->clearMessages();
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
            if ($this->_saveHtmlOnFailure) {
                $this->saveHtmlPage();
            }
            if ($this->_saveScreenOnFail) {
                $this->takeScreenshot();
            }
        }

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
        $helperClassNames = $this->_testConfig->getTestHelperNames();
        $helperName = ucwords($testScope);
        if (!isset($helperClassNames[$helperName])) {
            throw new UnexpectedValueException('Cannot load helper "' . $helperName . '"');
        }
        $helperClassName = $helperClassNames[$helperName];
        if (isset(self::$_testHelpers[$helperClassName])) {
            /** @var $helper Mage_Selenium_AbstractHelper */
            $helper = self::$_testHelpers[$helperClassName];
            $helper->setTestInstance($this);
            return $helper;
        }
        self::$_testHelpers[$helperClassName] = new $helperClassName($this);
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
        if (isset(self::$_testHelpers[$className])) {
            /** @var $helper Mage_Selenium_AbstractHelper */
            $helper = self::$_testHelpers[$className];
            $helper->setTestInstance($this);
            return $helper;
        }

        self::$_testHelpers[$className] = new $className($this);
        return self::$_testHelpers[$className];
    }

    /**
     * @param string $message
     */
    public function skipTestWithScreenshot($message)
    {
        $fileName = 'Skipped__' . $this->getTestId();
        $fileName = preg_replace('/"/', '\'', $fileName);
        $fileName = preg_replace('/ with data set #/', '__DataSet_', $fileName);
        $url = $this->takeScreenshot($fileName);
        $this->markTestSkipped($message . "\n" . $url);
    }

    /**
     * @return Mage_Selenium_Helper_Config
     */
    public function getConfigHelper()
    {
        return $this->_configHelper;
    }

    /**
     * @return Mage_Selenium_Helper_Params
     */
    public function getParamsHelper()
    {
        return $this->_paramsHelper;
    }

    /**
     * @return Mage_Selenium_Helper_Uimap
     */
    public function getUimapHelper()
    {
        return $this->_uimapHelper;
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
        $titleArr = explode('/', $url);
        if (in_array($paramName, $titleArr) && isset($titleArr[array_search($paramName, $titleArr) + 1])) {
            return $titleArr[array_search($paramName, $titleArr) + 1];
        }
        foreach ($titleArr as $key => $value) {
            if (preg_match("#$paramName$#i", $value) && isset($titleArr[$key + 1])) {
                return $titleArr[$key + 1];
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
        $urlFromTitleAttr = $this->getElement($locator)->attribute('title');
        if (is_numeric($urlFromTitleAttr)) {
            return $urlFromTitleAttr;
        }

        return $this->defineIdFromUrl($urlFromTitleAttr);
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
        $element = $this->getControlElement($fieldType, $fieldName);
        if ($fieldType == self::FIELD_TYPE_COMPOSITE_MULTISELECT) {
            $element = $this->getChildElement($element, '../select');
        }
        $fieldId = $element->attribute('id');
        if (!$fieldId) {
            $fieldId = $element->attribute('name');
        }
        if (!$fieldId) {
            $fieldId = $element->attribute('for');
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
     * @param array|string $testData
     * @return array
     */
    public function fixtureDataToArray($testData)
    {
        if (is_string($testData)) {
            $elements = explode('/', $testData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $testData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        return $testData;
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
                $errorMessages[] = "Value for '" . $fieldKey . "' " . $messageParam
                    . " is not changed: [There is no this " . $messageParam . " in dataset]";
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
     */
    public function setDataParams(&$value)
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
            $value = preg_replace('/%currentDate%/', date("n/j/Y"), $value);
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
        if (preg_match('/%design_theme%/', $value)) {
            $value = preg_replace('/%design_theme%/', $this->_configHelper->getApplicationDesignTheme(), $value);
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
        if ($type && array_key_exists($type, $this->_messages)) {
            unset($this->_messages[$type]);
        } elseif ($type == null) {
            $this->_messages = array();
        }
    }

    /**
     * Gets all messages on the pages
     */
    public function _parseMessages()
    {
        $area = $this->getArea();
        $page = $this->getCurrentUimapPage();
        if ($area == 'admin' || $area == 'frontend') {
            $this->_messages['notice'] = $this->getElementsValue($page->findMessage('general_notice'), 'text');
            $this->_messages['validation'] = $this->parseValidationMessages();
        } else {
            $this->_messages['validation'] = $this->getElementsValue($page->findMessage('general_validation'), 'text');
        }
        $this->_messages['success'] = $this->getElementsValue($page->findMessage('general_success'), 'text');
        $this->_messages['error'] = $this->getElementsValue($page->findMessage('general_error'), 'text');
        foreach ($this->_messages as $messageType => $messages) {
            $this->_messages[$messageType] = array_diff($messages, array(''));
        }
    }

    /**
     * @return array
     */
    public function parseValidationMessages()
    {
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $tab */
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $message */
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $nameElement */
        $messageLocator = $this->getCurrentUimapPage()->findMessage('general_validation');
        $tabsWithErrors = $this->getElements("//a[contains(@class,'error')]", false);
        $messages = array();
        if (empty($tabsWithErrors)) {
            $validationMessages = $this->getElements($messageLocator, false);
            foreach ($validationMessages as $message) {
                if (!$message->displayed()) {
                    continue;
                }
                $fieldId = $message->attribute('for');
                $this->addParameter('fieldId', $fieldId);
                $nameElement = $this->elementIsPresent($this->_getControlXpath('pageelement', 'field_label'));
                $fieldName = ($nameElement) ? trim($nameElement->text(), " *\t\n\r") : $fieldId;
                $messages[] = '"' . $fieldName . '": ' . $message->text();
            }
        } else {
            foreach ($tabsWithErrors as $tab) {
                $isTabOpened = $this->getChildElement($tab, '..')->attribute('aria-selected');
                $isTabActive = strpos($tab->attribute('class'), 'active');
                //Expand Advanced Settings block on Product Page if need
                if (!$tab->displayed()) {
                    $this->getChildElement($tab, 'ancestor::div/h3')->click();
                    $this->waitForElementStopsMoving($tab);
                }
                if ($isTabOpened == 'false' || $isTabActive === false) {
                    $waitAjax = strpos($tab->attribute('class'), 'ajax');
                    $this->focusOnElement($tab);
                    $tab->click();
                    if ($waitAjax !== false) {
                        $this->pleaseWait();
                    }
                }
                $validationMessages = $this->getElements($messageLocator, false);
                foreach ($validationMessages as $message) {
                    if (!$message->displayed()) {
                        continue;
                    }
                    $fieldId = $message->attribute('for');
                    $this->addParameter('fieldId', $fieldId);
                    $nameElement = $this->elementIsPresent($this->_getControlXpath('pageelement', 'field_label'));
                    $fieldName = ($nameElement) ? trim($nameElement->text(), " *\t\n\r") : $fieldId;
                    $messages[] = '"' . $fieldName . '": ' . $message->text();
                }
            }
        }
        return $messages;
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
                if (isset($this->_messages[$message])) {
                    $messages = array_merge($messages, $this->_messages[$message]);
                }
            }
            return $messages;
        }

        return $this->_messages;
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
            return (isset($this->_messages[$type])) ? $this->_messages[$type] : null;
        }
        return $this->_messages;
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
                $this->_messages[$type][] = $value;
            }
        } else {
            $this->_messages[$type][] = $message;
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
     * @return int Number of nodes that match the specified $locator
     */
    public function verifyMessagesCount($count = 1, $locator = null)
    {
        if ($locator === null) {
            $locator = $this->_getMessageXpath('general_validation');
        }
        if ($this->elementIsPresent("//a[contains(@class,'error')]")) {
            return count($this->getElements($locator, false)) == $count;
        }
        $actualCount = 0;
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        foreach ($this->getElements($locator, false) as $element) {
            if ($element->displayed()) {
                $actualCount++;
            }
        }
        return $actualCount == $count;
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
        return array(
            'success' => false,
            'locator' => $locator,
            'found' => self::messagesToString($this->getMessagesOnPage())
        );
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
     * @param null|string $message Validation message ID from UIMap OR XPath of validation message(by default = null)
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
            $location = $this->locationToString();
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
                $error = '"' . $type . '" message(s) is on the page:';
            } else {
                $error = '"' . $message . '" message(s) is on the page.';
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
    public static function messagesToString($message)
    {
        if (is_array($message) && $message) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }
        return $message;
    }

    /**
     * @return string
     */
    public function locationToString()
    {
        return "\nCurrent url: '" . $this->url() . "'\nCurrent page: '" . $this->getCurrentPage()
            . "'\nCurrent area: '" . $this->getArea() . "'\n";
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
     * Get additional prefix for navigation
     *
     * @return string
     */
    public function getUrlPostfix()
    {
        if (!is_null($this->_urlPostfix)) {
            return $this->_urlPostfix;
        }
        return '';
    }

    /**
     * Set additional prefix for navigation
     *
     * @param string $area
     * @param string $urlPrefix your params to add to URL (exp: /vde/frontPage)
     */
    public function setUrlPrefix($area, $urlPrefix)
    {
        $this->_urlPrefix[$area] = $urlPrefix;
    }

    /**
     * Get additional prefix for navigation
     *
     * @param string $area
     * @return string
     */
    public function getUrlPrefix($area = 'frontend')
    {
        if (isset($this->_urlPrefix[$area]) && !is_null($this->_urlPrefix[$area])) {
            return $this->_urlPrefix[$area];
        }
        return '';
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
            $url = $this->_uimapHelper->getPageMca($area, $page, $this->_paramsHelper);
            $baseUrl = $this->_configHelper->getBaseUrl();
            $url = $baseUrl . $this->getUrlPrefix($area) . $url . $this->getUrlPostfix();
            $this->url($url);
        }
        $this->waitForAjax();
        if ($validatePage) {
            $this->validatePage($page);
        } else {
            $this->setCurrentPage($this->_findCurrentPageFromUrl());
            $this->closeSystemMessagesDialog();
        }

        return $this;
    }

    /**
     * Navigate to the specified admin page.<br>
     * Page identifier must be described in the UIMap. Opens "Dashboard" page by default.
     *
     * @param string $page Page identifier (by default = null)
     * @param bool $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function admin($page = null, $validatePage = true)
    {
        $page = (is_null($page)) ? $this->pageAfterAdminLogin : $page;
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
     * Select last opened window
     * @return string
     */
    public function selectLastWindow()
    {
        $windowHandles = $this->windowHandles();
        $windowHandle = end($windowHandles);
        $this->window($windowHandle);
        $this->waitForPageToLoad();

        return $windowHandle;
    }

    /**
     * Close last opened window
     */
    public function closeLastWindow()
    {
        $windowHandles = $this->windowHandles();
        if (count($windowHandles) > 1) {
            $this->window(end($windowHandles));
            $this->closeWindow();
            $this->window('');
            $this->validatePage();
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
        $currentUrl = preg_replace('#backend(/backend|/admin)?/?#', 'backend/', $currentUrl);
        $possibleAreas = array();
        foreach ($areasConfig as $area => $areaConfig) {
            $areaUrl =
                preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $areaConfig['url']));
            //@TODO Fix for StoreLauncher tests
            $areaUrl = preg_replace('#backend/(backend|admin)/?$#', 'backend/', $areaUrl);
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
        $currentUrl = str_replace($this->getUrlPrefix(), '', $currentUrl);
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
    public function _findCurrentPageFromUrl($url = null)
    {
        if (is_null($url)) {
            $url = $this->url();
            $url = str_replace(
                array(
                    $this->getUrlPostfix(),
                    $this->getUrlPrefix()
                ), '', $url
            );
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
            $this->checkCurrentPage($page);
            $this->assertEmptyVerificationErrors();
        } else {
            $page = $this->_findCurrentPageFromUrl();
        }
        $this->assertEmptyPageErrors();
        $expectedTitle = $this->getUimapPage($this->_configHelper->getArea(), $page)->getTitle($this->_paramsHelper);
        $actualTitle = $this->title();
        if (!is_null($expectedTitle) && $expectedTitle !== $actualTitle) {
            $errorMessage = "\nCurrent url: '" . $this->url() . "'\nCurrent area: '" . $this->getArea() . "'\n"
                . 'Title for page "' . $page . '" is unexpected:' . "('$expectedTitle' != '$actualTitle')";
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            if (strlen($messagesOnPage) > 0) {
                $errorMessage .= "\nMessages on current page:\n" . $messagesOnPage;
            }
            $this->fail($errorMessage);
        }
        $this->setCurrentPage($page);
        $this->closeSystemMessagesDialog();
    }

    public function assertEmptyPageErrors()
    {
        $errorMessages = array(
            'Fatal error', 'Parse error', 'was not found', 'Unable to connect',
            'There has been an error processing your request',
            'The page you requested was not found',
            "The page isn't redirecting properly",
            'If you typed the URL directly',
            'Internal server error', 'Notice: ', 'Warning: '
        );
        $pageText = $this->getElement('/*')->text();
        foreach ($errorMessages as $message) {
            if (strpos($pageText, $message) !== false) {
                $this->fail('Page validation is not done, because there is  message "' . $message . '" on it');
            }
        }
        if ($this->controlIsVisible('message', 'general_js_error')) {
            $this->fail($this->locationToString() . 'JS error on page.');
        }
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

        //@TODO Fix for StoreLauncher tests
        $baseUrl = preg_replace('#backend/(backend|admin)/?$#', 'backend/', $baseUrl);
        $currentUrl = preg_replace('#backend(/backend|/admin)?/?#', 'backend/', $currentUrl);
        if (strpos($currentUrl, $baseUrl) !== false) {
            $mca = trim(substr($currentUrl, strlen($baseUrl)), " /\\#");
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
        //
        $mca = preg_replace('|/form_key/[A-Za-z0-9]+/?|', '/', $mca);
        //Delete action part of mca if it's ?SID=
        $mca = preg_replace('|(\?)?SID=([a-zA-Z\d]+)?|', '', $mca);

        return preg_replace('|^/|', '', $mca);
    }

    /**
     * @param null $url
     *
     * @return string
     */
    public function getMcaFromUrl($url = null)
    {
        if (is_null($url)) {
            $url = $this->url();
        }
        $areasConfig = $this->_configHelper->getConfigAreas();
        return self::_getMcaFromCurrentUrl($areasConfig, $url);
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
    public function _findUimapElement($elementType, $elementName, $uimap = null)
    {
        $fieldSetsNotInTab = null;
        $errorMessage = null;
        $returnValue = null;
        if (is_null($uimap)) {
            if ($elementType == 'button') {
                $generalButtons = $this->getCurrentUimapPage()->getMainButtons();
                if (isset($generalButtons[$elementName])) {
                    return $this->_paramsHelper->replaceParameters($generalButtons[$elementName]);
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
            $errorMessage = $this->locationToString() . $e->getMessage() . " - '" . $elementName . "'";
        }
        if (isset($e) && $fieldSetsNotInTab != null) {
            foreach ($fieldSetsNotInTab as $fieldset) {
                try {
                    $returnValue = $fieldset->$method($elementName, $this->_paramsHelper);
                    break;
                } catch (Exception $_e) {
                }
            }
        }
        if ($errorMessage != null && $returnValue === null) {
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            if (strlen($messagesOnPage) > 0) {
                $errorMessage .= "\nMessages on current page:\n" . $messagesOnPage;
            }
            $this->fail($errorMessage);
        }
        return $returnValue;
    }

    /**
     * Get part of UIMap for opened tab
     * @throws RuntimeException
     * @return Mage_Selenium_Uimap_Tab
     */
    public function _getActiveTabUimap()
    {
        $tabsOnPage = false;
        $tabData = $this->getCurrentUimapPage()->getAllTabs($this->_paramsHelper);
        /** @var Mage_Selenium_Uimap_Tab $tabUimap */
        foreach ($tabData as $tabUimap) {
            $tabsOnPage = true;
            $availableElement = $this->elementIsPresent($tabUimap->getXPath());
            if ($availableElement) {
                $tabClass = $availableElement->attribute('class');
                $parentClass = $this->getChildElement($availableElement, '..')->attribute('class');
                if (strpos($tabClass, 'active') !== false || strpos($parentClass, 'active') !== false) {
                    return $tabUimap;
                }
            }
        }
        if ($tabsOnPage && $this->getArea() == 'admin') {
            throw new RuntimeException($this->locationToString() . 'It\'s impossible to define active tab');
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
    public function _getControlXpath($controlType, $controlName, $uimap = null)
    {
        if ($controlType === 'message') {
            return $this->_getMessageXpath($controlName);
        }
        $locator = $this->_findUimapElement($controlType, $controlName, $uimap);
        if (is_object($locator) && method_exists($locator, 'getXPath')) {
            $locator = $locator->getXPath($this->_paramsHelper);
        }

        return $locator;
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
                if ($controlType == self::FIELD_TYPE_DROPDOWN) {
                    $elementValue = $this->select($element)->selectedValue();
                } elseif ($controlType == self::FIELD_TYPE_MULTISELECT) {
                    $elementValue = $this->select($element)->selectedValues();
                } elseif ($controlType == self::FIELD_TYPE_RADIOBUTTON || $controlType == self::FIELD_TYPE_CHECKBOX) {
                    $elementValue = $element->selected();
                } else {
                    $elementValue = $element->attribute('value');
                }
                break;
            case 'selectedId':
                if ($controlType == self::FIELD_TYPE_DROPDOWN) {
                    $elementValue = $this->select($element)->selectedId();
                } elseif ($controlType == self::FIELD_TYPE_MULTISELECT) {
                    $elementValue = $this->select($element)->selectedIds();
                } else {
                    $elementValue = $element->attribute('id');
                }
                break;
            case 'selectedLabel':
                if ($controlType == self::FIELD_TYPE_DROPDOWN) {
                    $elementValue = trim($this->select($element)->selectedLabel(), chr(0xC2) . chr(0xA0) . ' ');
                } elseif ($controlType == self::FIELD_TYPE_MULTISELECT) {
                    $elementValue = $this->select($element)->selectedLabels();
                    foreach ($elementValue as $key => $label) {
                        $elementValue[$key] = trim($label, chr(0xC2) . chr(0xA0) . ' ');
                    }
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

        if (is_array($elementValue)) {
            $elementValue = array_map('trim', $elementValue);
        } elseif (!is_bool($elementValue)) {
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
    public function _getMessageXpath($message)
    {
        $messages = $this->getCurrentUimapPage()->getAllElements('messages');
        /** @var Mage_Selenium_Uimap_ElementsCollection $messages */
        $messageLocator = $messages->get($message, $this->_paramsHelper);
        if ($messageLocator === null) {
            $messagesOnPage = self::messagesToString($this->getMessagesOnPage());
            $errorMessage = $this->locationToString() . 'Message "' . $message . '" is not found';
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
    public function _getFormDataMap($fieldsets, $data)
    {
        $dataMap = array();
        $fieldsetsElements = array();
        foreach ($fieldsets as $fieldsetName => $fieldsetContent) {
            /** @var Mage_Selenium_Uimap_Fieldset $fieldsetContent */
            $fieldsetsElements[$fieldsetName] = $fieldsetContent->getFieldsetElements();
        }
        foreach ($data as $dataFieldName => $dataFieldValue) {
            if ($dataFieldValue == '%noValue%' || is_array($dataFieldValue)) {
                continue;
            }
            foreach ($fieldsetsElements as $fieldsetContent) {
                foreach ($fieldsetContent as $fieldsType => $fieldsData) {
                    if (array_key_exists($dataFieldName, $fieldsData)) {
                        $dataMap[$dataFieldName] = array(
                            'type' => $fieldsType,
                            'value' => $dataFieldValue,
                            'path' => $fieldsData[$dataFieldName]
                        );
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
    public function getDataMapForFill(array $dataToFill, $containerType, $containerName)
    {
        $containerUimap = $this->_findUimapElement($containerType, $containerName);
        $getMethod = 'get' . ucfirst(strtolower($containerType)) . 'Elements';
        $containerElements = $containerUimap->$getMethod($this->_paramsHelper);
        $fillData = array();
        foreach ($dataToFill as $fieldName => $fieldValue) {
            if ($fieldValue === '%noValue%' || is_array($fieldValue)) {
                $fillData['skipped'][$fieldName] = $fieldValue;
                continue;
            }
            foreach ($containerElements as $elementType => $elementsData) {
                if (array_key_exists($fieldName, $elementsData)) {
                    $fillData['isPresent'][] = array(
                        'type' => $elementType,
                        'name' => $fieldName,
                        'value' => $fieldValue,
                        'locator' => $elementsData[$fieldName]
                    );
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
     * @param string $url
     *
     * @return mixed
     * @throws RuntimeException
     */
    public function getFile($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $this->cookie()->get('PHPSESSID'));
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            $error = 'Error connection[' . curl_errno($curl) . '] to ' . $url . ': ' . curl_error($curl);
            throw new RuntimeException($error);
        }
        $data = substr($data, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
        $info = curl_getinfo($curl);
        if (!$info) {
            throw new RuntimeException("CURL error when accessing '$url': " . curl_error($curl));
        }
        curl_close($curl);
        return $data;
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
            $fileName = time() . '__' . $this->getTestId();
            $fileName = preg_replace('/"/', '\'', $fileName);
            $fileName = preg_replace('/ with data set #/', '__DataSet_', $fileName);
        }
        $filePath = $screenshotPath . $fileName . '.png';
        $screenshotContent = $this->currentScreenshot();
        $file = fopen($filePath, 'a+');
        fputs($file, $screenshotContent);
        fflush($file);
        fclose($file);
        if (file_exists($filePath)) {
            return 'Screenshot: ' . $filePath . "\n";
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
        $osName = $this->execute(array('script' => 'return navigator.appVersion;', 'args' => array()));
        if (preg_match('/Windows/i', $osName)) {
            return 'Windows';
        } elseif (preg_match('/Linux/i', $osName)) {
            return 'Linux';
        } elseif (preg_match('/Macintosh/i', $osName)) {
            return 'MacOS';
        }
        return 'Unknown OS';
    }

    /**
     * Returns correct path to screenshot save path.
     *
     * @return string
     */
    public function getScreenshotPath()
    {
        $path = $this->_screenshotPath;

        if (!in_array(substr($path, strlen($path) - 1, 1), array("/", "\\"))) {
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
            $this->fail($this->locationToString() . "Problem with $controlType '$controlName', xpath '$locator':\n"
                . 'Control is not present(visible) on the page');
        }
        $this->focusOnElement($availableElement);
        $this->getElement($locator)->click();
        if ($willChangePage) {
            $this->waitForPageToLoad();
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
        if (!$availableElement) {
            $this->fail("There is no way to click on control(There is no '$controlName' $controlType)");
        }
        $expectedMessage = $this->_getMessageXpath($message);
        $this->focusOnElement($availableElement);
        $this->getElement($locator)->click();
        $actualText = $this->alertText();
        $this->acceptAlert();
        $this->waitForAjax();
        if ($willChangePage) {
            $this->waitForPageToLoad();
            $this->validatePage();
        }
        $this->assertSame($expectedMessage, $actualText, 'The confirmation text incorrect');
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
     * @param mixed $uimap
     *
     * @return bool
     */
    public function controlIsPresent($controlType, $controlName, $uimap = null)
    {
        $locator = $this->_getControlXpath($controlType, $controlName, $uimap);
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
        if ($availableElement && $availableElement->enabled() && $availableElement->displayed()) {
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
     * Searches a control with the specified name and type on the page.
     * If the control is expanded, returns true; otherwise false.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @return bool
     */
    public function isControlExpanded($controlType, $controlName)
    {
        $fieldsetClass = $this->getControlAttribute($controlType, $controlName, 'class');
        return strpos($fieldsetClass, 'opened') !== false || strpos($fieldsetClass, 'active') !== false;
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
        $tabElement = $this->getControlElement('tab', $tabName);
        if (!$tabElement->displayed()) {
            $this->fail($this->locationToString() . "Problem with tab '$tabName':\nTab is not visible on the page");
        }
        $isTabOpened = $this->getChildElement($tabElement, '..')->attribute('aria-selected');
        $isTabActive = strpos($tabElement->attribute('class'), 'active');
        if ($isTabOpened == 'true' || $isTabActive !== false) {
            return;
        }
        $waitAjax = strpos($tabElement->attribute('class'), 'ajax');
        $this->focusOnElement($tabElement);
        $this->getControlElement('tab', $tabName)->click();
        if ($waitAjax !== false) {
            $this->pleaseWait();
            $this->assertEmptyPageErrors();
        }
        $openedTab = $this->_getActiveTabUimap()->getTabId();
        if ($openedTab !== $tabName) {
            $this->fail($this->locationToString() . "'$tabName' tab is not opened");
        }
    }

    /**
     * Returns number of nodes that match the specified xPath|css selector,
     * eg. "table" would give number of tables.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param string $locator
     *
     * @return int
     */
    public function getControlCount($controlType, $controlName, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath($controlType, $controlName);
        }
        return count($this->getElements($locator, false));
    }

    /**
     * Returns table column names
     *
     * @param string $tableLocator
     *
     * @return array
     */
    public function getTableHeadRowNames($tableLocator = '//table[@id]')
    {
        $headElements = $this->getElements($tableLocator . "//thead/tr");
        list($headElement) = $headElements;
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        if (count($headElements) > 1) {
            foreach ($headElements as $element) {
                if (trim($element->attribute('class')) == 'headings') {
                    $headElement = $element;
                    break;
                }
            }
        }
        $headNames = array();
        foreach ($this->getChildElements($headElement, 'th') as $element) {
            $headNames[] = $element->text();
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
        $columnId = array_search($columnName, $this->getTableHeadRowNames($tableXpath));
        if ($columnId === false) {
            $this->fail('There is no column with name "' . $columnName . '" in grid with locator ' . $tableXpath);
        }
        return $columnId + 1;
    }

    /*
    * Set sort order in grid
    *
    * @param string $tableColumnName Column name
    * @param string $tableOrder 'desc' or 'asc'
    * @param string $tableXpath Table xPath
    */
    public function sortTableOrderByColumn($tableColumnName, $sortOrder, $tableXpath = '//table[@id]')
    {
        //Get records count on the page
        $tableXpath .= "/thead//th//a[span='$tableColumnName']";
        $availableElement = $this->elementIsPresent($tableXpath);
        if ($availableElement) {
            if ($availableElement->attribute('class') == 'not-sort') {
                $this->focusOnElement($availableElement);
                $availableElement->click();
                $this->pleaseWait();
            }
            $element = $this->getElement($tableXpath);
            $currentOrder = $element->attribute('title');
            if ($currentOrder != $sortOrder) {
                $this->focusOnElement($availableElement);
                $element->click();
                $this->pleaseWait();
            }
        } else {
            $this->fail('ddd');
        }
    }

    /*
    * Get info from table as array
    *
    * @param array $tableHeaderNames Columns headers
    * @param string $tableXpath Table xPath
    *
    * @return array Associative array  Column Header => Value
    */
    public function getInfoInTable(array $tableHeaderNames, $tableXpath = '//table[@id]')
    {
        $columnsData = array();
        $tableValues = array();
        if (empty($tableHeaderNames)) {
            //Get all available columns
            $columnsData = $this->getTableHeadRowNames($tableXpath);
        } else {
            foreach ($tableHeaderNames as $tableHeaderName) {
                $id = $this->getColumnIdByName($tableHeaderName, $tableXpath);
                $columnsData[$id] = $tableHeaderName;
            }
        }
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $line */
        $tableElements = $this->getElements($tableXpath . '/tbody/tr', false);
        foreach ($tableElements as $key => $line) {
            $rowValues = array();
            foreach ($columnsData as $columnIndex => $columnName) {
                $rowValues[$columnName] = $this->getChildElement($line, "td[$columnIndex]");
            }
            $tableValues[$key] = $rowValues;
        }
        return $tableValues;
    }

    /**
     * @param string $controlType
     * @param string $controlName
     * @return int
     */
    public function getTotalRecordsInTable($controlType, $controlName)
    {
        $fieldset = $this->getControlElement($controlType, $controlName);
        list(, , $totalCount) = explode('|', $this->getChildElement($fieldset, "//div[@class='pager']")->text());
        return preg_replace('/[\D]+/', '', $totalCount);
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
    public static function combineLocatorsToOne(array $locators)
    {
        $values = array_values($locators);
        $locatorDeterminants = array('//' => '|', 'css=' => ', ');
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
            $timeout = $this->_browserTimeout;
        }
        if (is_array($locator)) {
            $output = "\nNone of the elements are not present on the page. \nLocators: \n" . implode("\n", $locator);
            $locator = self::combineLocatorsToOne($locator);
        } else {
            $output = "\nElement is not present on the page. \nLocator: " . $locator;
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            $availableElement = $this->elementIsPresent($locator);
            if ($availableElement) {
                return $availableElement;
            }
            usleep(500000);
        }
        $this->assertEmptyPageErrors();
        throw new RuntimeException($this->locationToString() . 'Timeout after ' . $timeout . ' seconds' . $output);
    }

    /**
     * Wait for control is present
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param int|null $timeout
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForControl($controlType, $controlName, $timeout = null)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        return $this->waitForElement($locator, $timeout);
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
            $timeout = $this->_browserTimeout;
        }
        if (is_array($locator)) {
            $output = "\nNone of the elements(or alert) are not present on the page. \nLocators: \n"
                . implode("\n", $locator);
            $locator = self::combineLocatorsToOne($locator);
        } else {
            $output = "\nElement(or alert) is not present on the page. \nLocator: " . $locator;
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            try {
                if ($this->alertIsPresent()) {
                    return true;
                }
                if ($this->elementIsPresent($locator)) {
                    return true;
                }
                usleep(500000);
            } catch (Exception $e) {
            }
        }
        $this->assertEmptyPageErrors();
        throw new RuntimeException($this->locationToString() . 'Timeout after ' . $timeout . ' seconds' . $output);
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
            $timeout = $this->_browserTimeout;
        }
        if (is_array($locator)) {
            $output = "\nNone of the elements are not visible or not present on the page. \nLocators: \n"
                . implode("\n", $locator);
            $locator = self::combineLocatorsToOne($locator);
        } else {
            $output = "\nElement is not visible or not present on the page. \nLocator: " . $locator;
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            try {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $availableElement */
                $availableElements = $this->getElements($locator, false);
                foreach ($availableElements as $availableElement) {
                    if ($availableElement->displayed()) {
                        return $availableElement;
                    }
                }
                usleep(500000);
            } catch (Exception $e) {
            }
        }
        $this->assertEmptyPageErrors();
        throw new RuntimeException($this->locationToString() . 'Timeout after ' . $timeout . ' seconds' . $output);
    }

    /**
     * Waits for the element(s) to be invisible
     *
     * @param string|array $locator XPath locator or array of locator's
     * @param int $timeout Timeout period in seconds (by default = null)
     *
     * @throws RuntimeException
     * @return bool
     */
    public function waitForElementNotVisible($locator, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeout;
        }
        if (is_array($locator)) {
            $output = "\nNone of the elements are invisible on the page. \nLocators: \n" . implode("\n", $locator);
            $locator = self::combineLocatorsToOne($locator);
        } else {
            $output = "\nElement is visible on the page. \nLocator: " . $locator;
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            try {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $availableElement */
                $availableElements = $this->getElements($locator, false);
                if (empty($availableElements)) {
                    return true;
                }
                foreach ($availableElements as $availableElement) {
                    if (!$availableElement->displayed()) {
                        return true;
                    }
                }
                usleep(500000);
            } catch (Exception $e) {
            }
        }
        $this->assertEmptyPageErrors();
        throw new RuntimeException($this->locationToString() . 'Timeout after ' . $timeout . ' seconds' . $output);
    }

    /**
     * Wait for control is not visible
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param int|null $timeout
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForControlNotVisible($controlType, $controlName, $timeout = null)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        return $this->waitForElementNotVisible($locator, $timeout);
    }

    /**
     * Wait for control is visible
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param int|null $timeout
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForControlVisible($controlType, $controlName, $timeout = null)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        return $this->waitForElementVisible($locator, $timeout);
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
            $timeout = $this->_browserTimeout;
        }
        if (is_array($locator)) {
            $output = "\nNone of the elements are not editable. \nLocators: \n" . implode("\n", $locator);
            $locator = self::combineLocatorsToOne($locator);
        } else {
            $output = "\nElement is not editable. \nLocator: " . $locator;
        }
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            try {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $availableElement */
                $availableElements = $this->getElements($locator, false);
                foreach ($availableElements as $availableElement) {
                    if ($availableElement->enabled() && $availableElement->displayed()) {
                        return $availableElement;
                    }
                }
                usleep(500000);
            } catch (Exception $e) {
            }
        }
        $this->assertEmptyPageErrors();
        throw new RuntimeException($this->locationToString() . 'Timeout after ' . $timeout . ' seconds' . $output);
    }

    /**
     * Wait for control is editable
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param int|null $timeout
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForControlEditable($controlType, $controlName, $timeout = null)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        return $this->waitForElementEditable($locator, $timeout);
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
            $timeout = $this->_browserTimeout;
        }
        $ajax = 'var ajax = 0;var jquery = 0;'
            . 'if (typeof window.Ajax != "undefined") {ajax = window.Ajax.activeRequestCount;}'
            . 'if (typeof window.jQuery != "undefined") {jquery = window.jQuery.active;} return ajax + jquery;';
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            usleep(500000);
            if ($this->execute(array('script' => $ajax, 'args' => array())) === 0) {
                return;
            }
        }
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
        if (!is_null($tabUimap)) {
            $tabName = $tabUimap->getTabId();
            $this->addParameter('tab', $this->getControlAttribute('tab', $tabName, 'name'));
        }
        $this->clickControlAndWaitMessage($controlType, $controlName);
        $this->pleaseWait();
        if (!is_null($tabUimap)) {
            $this->openTab($tabName); //@TODO remove when fixed for shopping cart price rules
            $this->assertSame($tabName, $this->_getActiveTabUimap()->getTabId(),
                $this->locationToString() . 'Opened wrong tab after Save and Continue Edit action');
        }
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
        $this->waitForAjax();
        $this->waitForElementVisible($messagesXpath);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->addParameter('store', $this->defineParameterFromUrl('store'));
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
            $this->_messages[$key] = array_unique($value);
        }
        if (is_string($types)) {
            $types = explode(',', $types);
            $types = array_map('trim', $types);
        }
        $returnXpath = array();
        foreach ($types as $message) {
            ${$message} = $this->_getMessageXpath('general_' . $message);
            if (array_key_exists($message, $this->_messages)) {
                $exclude = '';
                foreach ($this->_messages[$message] as $messageText) {
                    if (strpos($messageText, "'") !== false) {
                        $messageText = "concat('" . str_replace('\'', "',\"'\",'", $messageText) . "')";
                    } else {
                        $messageText = "'" . $messageText . "'";
                    }
                    $exclude .= "[not(normalize-space(..//.)=$messageText)]";
                }
                ${$message} .= $exclude;
            }
            $returnXpath[] = ${$message};
        }
        return (count($returnXpath) == 1) ? $returnXpath[0] : $returnXpath;
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
        $data = $this->_prepareDataForSearch($data);
        $trLocator = $this->search($data, $fieldSetName);

        if ($trLocator) {
            $element = $this->getElement($trLocator . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
            if ($willChangePage) {
                $itemId = $this->defineIdFromTitle($trLocator);
                $this->addParameter('id', $itemId);
                $element->click();
                $this->waitForPageToLoad();
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
        $data = $this->_prepareDataForSearch($data);
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
    public function _prepareDataForSearch(array $data, $checkFields = array(self::FIELD_TYPE_DROPDOWN => 'website'))
    {
        foreach ($checkFields as $fieldType => $fieldName) {
            if (array_key_exists($fieldName, $data) && !$this->controlIsVisible($fieldType, $fieldName)) {
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
        $trLocator = $fieldsetLocator . $this->formSearchXpath($data);
        if ($this->elementIsPresent($trLocator)) {
            return $trLocator;
        }
        $resetButtonElement = $this->getControlElement('button', 'reset_filter', $fieldsetUimap);
        $useAjax = preg_replace('/\.[\D]+\(\)/', '', $resetButtonElement->attribute('onclick')) . ".useAjax = ''";
        $pageToLoad = $this->elementIsPresent('//script[contains(text(),"' . $useAjax . '")]');
        $this->focusOnElement($resetButtonElement);
        $resetButtonElement->click();
        if (!$pageToLoad) {
            $this->pleaseWait();
        } else {
            $this->waitForPageToLoad();
            $this->validatePage();
        }
        if ($this->elementIsPresent($trLocator)) {
            return $trLocator;
        }
        $this->addParameter('tableXpath', $fieldsetLocator);
        $noRecordsLocator = $this->_getMessageXpath('specific_table_no_records_found');
        $anyDataTrLocator = $this->_getControlXpath('pageelement', 'specific_table_default_row');
        if ($this->elementIsPresent($noRecordsLocator) || !$this->elementIsPresent($anyDataTrLocator)) {
            return null;
        }
        $qtyElementsInTable = $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'qtyElementsInTable');
        $totalCount = $this->getTotalRecordsInTable('fieldset', $fieldsetName);
        $pagerLocator = $fieldsetLocator . $qtyElementsInTable . "[not(text()='" . $totalCount . "')]";
        // Fill in search form and click 'Search' button
        $this->fillFieldset($data, $fieldsetName);
        $searchElement = $this->getControlElement('button', 'search', $fieldsetUimap);
        $this->focusOnElement($searchElement);
        $searchElement->click();
        $this->pleaseWait();
        $this->waitForElement(array($noRecordsLocator, $trLocator, $pagerLocator));

        return $this->elementIsPresent($trLocator) ? $trLocator : null;
    }

    /**
     * Forming xpath that contains the data to look up
     *
     * @param array $data Array of data to look up
     * @param string $trLocator
     *
     * @return string
     */
    public function formSearchXpath(array $data, $trLocator = null)
    {
        if (is_null($trLocator)) {
            $trLocator = $this->_getControlXpath('pageelement', 'default_table_row');
        }
        foreach ($data as $key => $value) {
            if (!preg_match('/_from/', $key) && !preg_match('/_to/', $key) && !is_array($value)) {
                if (strpos($value, "'") !== false) {
                    $value = "concat('" . str_replace('\'', "',\"'\",'", $value) . "')";
                } else {
                    $value = "'" . $value . "'";
                }
                $trLocator .= "[td[contains(text(),$value)]]";
            }
        }
        return $trLocator;
    }

    /**
     * Fill fieldset
     *
     * @param array $data
     * @param string $fieldsetId
     * @param bool $failXpathNotFound
     *
     * @return bool
     */
    public function fillFieldset(array $data, $fieldsetId, $failXpathNotFound = true)
    {
        $this->assertNotEmpty($data);
        $fillData = $this->getDataMapForFill($data, 'fieldset', $fieldsetId);

        if (!isset($fillData['isPresent']) && !$failXpathNotFound) {
            return false;
        }

        if (isset($fillData['isNotPresent']) && $failXpathNotFound) {
            $message = $this->locationToString() . "There are no fields in '" . $fieldsetId . "' fieldset:\n" .
                implode("\n", array_keys($fillData['isNotPresent']));
            $this->fail($message);
        }

        if (isset($fillData['isPresent'])) {
            foreach ($fillData['isPresent'] as $fieldData) {
                $this->_fill($fieldData);
            }
        }
        return true;
    }

    /**
     * Fill tab
     *
     * @param array $data
     * @param string $tabId
     * @param bool $failXpathNotFound
     *
     * @return bool
     */
    public function fillTab(array $data, $tabId, $failXpathNotFound = true)
    {
        $this->assertNotEmpty($data);
        $fillData = $this->getDataMapForFill($data, 'tab', $tabId);

        if (!isset($fillData['isPresent']) && !$failXpathNotFound) {
            return false;
        }

        if (isset($fillData['isNotPresent']) && $failXpathNotFound) {
            $message = $this->locationToString() . "There are no fields in '" . $tabId . "' tab:\n" .
                implode("\n", array_keys($fillData['isNotPresent']));
            $this->fail($message);
        }

        if (isset($fillData['isPresent'])) {
            $this->openTab($tabId);
            foreach ($fillData['isPresent'] as $fieldData) {
                $this->_fill($fieldData);
            }
        }
        return true;
    }

    /**
     * Fills any form with the provided data. Specific Tab can be filled only if $tabId is provided.
     *
     * @param array|string $data Array of data to fill or data source name
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
            throw new OutOfRangeException($this->locationToString() . 'Can not find main form in UIMap array');
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
                        throw new PHPUnit_Framework_Exception('Unsupported field type ' . $formField['type']);
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
     * @param array|string $data Array of data to verify or data source name
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
            throw new OutOfRangeException($this->locationToString() . 'Can not find main form in UIMap array');
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
            if (!$availableElement) {
                $this->addVerificationMessage(
                    'Can not find ' . $formField['type'] . ' (xpath: ' . $formField['path'] . ')');
                $resultFlag = false;
                continue;
            }
            $this->focusOnElement($availableElement);
            switch ($formField['type']) {
                case self::FIELD_TYPE_INPUT:
                    $actualValue = $availableElement->value();
                    if ((string)$actualValue != (string)$formField['value']) {
                        $this->addVerificationMessage($formFieldName
                            . ": The stored value is not equal to specified: ('" . $formField['value']
                            . "' != '" . $actualValue . "')");
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_CHECKBOX:
                case self::FIELD_TYPE_RADIOBUTTON:
                    $actualValue = ($availableElement->selected()) ? 'yes' : 'no';
                    if ($actualValue != strtolower($formField['value'])) {
                        $this->addVerificationMessage($formFieldName
                            . ": The stored value is not equal to specified: ('" . $formField['value']
                            . "' != '" . $actualValue . "')");
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_DROPDOWN:
                    $actualValue = trim($this->select($availableElement)->selectedLabel(),
                        chr(0xC2) . chr(0xA0) . ' ');
                    if ($actualValue != $formField['value']) {
                        $this->addVerificationMessage($formFieldName
                            . ": The stored value is not equal to specified: ('" . $formField['value']
                            . "' != '" . $actualValue . "')");
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_MULTISELECT:
                    $selectedLabels = $this->select($availableElement)->selectedLabels();
                    $selectedLabels = array_map('trim', $selectedLabels, array(" \t\n\r\0\x0B"));
                    $selectedLabels = array_map('trim', $selectedLabels);
                    if (strtolower($formField['value']) == 'all') {
                        $expectedLabels = $this->select($availableElement)->selectOptionLabels();
                    } else {
                        $expectedLabels = explode(',', $formField['value']);
                    }
                    $expectedLabels = array_map('trim', $expectedLabels, array(" \t\n\r\0\x0B"));
                    $expectedLabels = array_map('trim', $expectedLabels);
                    $expectedLabels = array_diff($expectedLabels, array(''));
                    foreach ($expectedLabels as $value) {
                        if (!in_array($value, $selectedLabels)) {
                            $this->addVerificationMessage($formFieldName
                                . ": The value '" . $value . "' is not selected. (Selected values are: '"
                                . implode(', ', $selectedLabels) . "')");
                            $resultFlag = false;
                        }
                    }
                    if (count($selectedLabels) != count($expectedLabels)) {
                        $this->addVerificationMessage("Amounts of the expected options are not equal to selected: ('"
                            . $formField['value'] . "' != '" . implode(', ', $selectedLabels) . "')");
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_COMPOSITE_MULTISELECT:
                    $result = $this->verifyCompositeMultiselect($formFieldName, array($formField['value']));
                    $resultFlag = ($result) ? $resultFlag : false;
                    break;
                case self::FIELD_TYPE_PAGEELEMENT:
                    $actualValue = trim($availableElement->text());
                    if ($actualValue != $formField['value']) {
                        $this->addVerificationMessage($formFieldName
                            . ": The stored value is not equal to specified: ('" . $formField['value']
                            . "' != '" . $actualValue . "')");
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
    public function _fill($fieldData)
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
            case self::FIELD_TYPE_COMPOSITE_MULTISELECT:
                $this->fillCompositeMultiselect($fieldData['name'], $fieldData['value'], $fieldData['locator']);
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
    public function fillField($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_INPUT, $name);
        }
        $element = $this->waitForElementEditable($locator, 10);
        $currentValue = $element->value();
        if ((string)$currentValue != (string)$value) {
            $this->focusOnElement($element);
            $element->clear();
            $element->value($value);
            $this->clearActiveFocus();
            $this->waitForAjax();
        }
    }

    /**
     * Fills 'multiselect' control by selecting the specified values.
     *
     * @param string $name
     * @param string $value
     * @param string|null $locator
     *
     * @throws RuntimeException
     */
    public function fillMultiselect($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_MULTISELECT, $name);
        }
        $element = $this->waitForElementEditable($locator, 10);
        $this->focusOnElement($element);
        $this->select($element)->clearSelectedOptions();
        if (strtolower($value) == 'all') {
            $options = $this->select($element)->selectOptionValues();
        } else {
            $options = explode(',', $value);
            $options = array_map('trim', $options);
        }
        foreach ($options as $value) {
            if ($value == '') {
                continue;
            }
            $optionLocators = array(
                "//option[normalize-space(text())='$value']",
                "//option[normalize-space(@value)='$value']",
                "//option[contains(text(),'$value')]"
            );
            foreach ($optionLocators as $optionLocator) {
                if ($this->elementIsPresent($locator . $optionLocator)) {
                    $this->select($element)->selectOptionByCriteria($this->using('xpath')->value('.' . $optionLocator));
                    continue 2;
                }
            }
            $this->fail('Option with name "' . $value . '" is not exist in "' . $name . '" multiselect field');
        }
        $this->clearActiveFocus();
        $this->waitForAjax();
    }

    /**
     * Fills the 'dropdown' control by selecting the specified value.
     *
     * @param string $name
     * @param string $value
     * @param string|null $locator
     * @param bool $confirmation
     *
     * @throws RuntimeException
     * @return void
     */
    public function fillDropdown($name, $value, $locator = null, $confirmation = false)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_DROPDOWN, $name);
        }
        $element = $this->waitForElementEditable($locator, 10);
        $optionLocators = array(
            "//option[normalize-space(text())='$value']",
            "//option[normalize-space(@value)='$value']",
            "//option[contains(text(),'$value')]"
        );
        foreach ($optionLocators as $optionLocator) {
            if ($this->elementIsPresent($locator . $optionLocator)) {
                $this->focusOnElement($element);
                $this->select($element)->selectOptionByCriteria($this->using('xpath')->value('.' . $optionLocator));
                if ($confirmation) {
                    $this->acceptAlert();
                }
                $this->clearActiveFocus();
                $this->waitForAjax();
                return;
            }
        }
        throw new RuntimeException('Option with value "' . $value . '" is not present in "' . $name . '" dropdown');
    }

    /**
     * @param string $name
     * @param string $value
     * @param string|null $locator
     *
     * @throws RuntimeException
     */
    public function fillCheckbox($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_CHECKBOX, $name);
        }
        $element = $this->waitForElementEditable($locator, 10);
        $isSelected = $element->selected();
        $value = strtolower($value);
        if (($value == 'yes' && !$isSelected) || ($value == 'no' && $isSelected)) {
            $this->focusOnElement($element);
            $element->click();
            $this->clearActiveFocus();
            $this->waitForAjax();
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @param string|null $locator
     *
     * @throws RuntimeException
     */
    public function fillRadiobutton($name, $value, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_RADIOBUTTON, $name);
        }
        $element = $this->waitForElementEditable($locator, 10);
        $isSelected = $element->selected();
        $value = strtolower($value);
        if (($value == 'yes' && !$isSelected) || ($value == 'no' && $isSelected)) {
            $this->focusOnElement($element);
            $element->click();
            $this->clearActiveFocus();
            $this->waitForAjax();
        }
    }

    /**
     * Fill CompositeMultiselect
     *
     * @param string $fieldName
     * @param string|array $fieldValue
     * @param null|string $locator
     */
    public function fillCompositeMultiselect($fieldName, $fieldValue, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, $fieldName);
        }
        if (is_string($fieldValue)) {
            $fieldValue = explode(',', $fieldValue);
        }
        $fieldValue = array_map('trim', $fieldValue);
        $generalElement = $this->getElement($locator);
        $generalElement->click();
        //Get all available options
        /* @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        $existValues = array();
        foreach ($this->getChildElements($generalElement, '//label/span') as $element) {
            $existValues[] = trim($element->text());
        }
        //Sort options for filling by type
        $isNeedAdd = array_diff($fieldValue, $existValues);
        $isUnselect = array_diff($existValues, $fieldValue);
        $isSelect = array_diff($fieldValue, $isNeedAdd);
        //Select options
        $optionLocator = "//label[span='%s']/%s";
        foreach ($isSelect as $label) {
            if (!$this->getChildElement($generalElement, sprintf($optionLocator, $label, 'input'))->selected()) {
                $this->getChildElement($generalElement, sprintf($optionLocator, $label, 'span'))->click();
            }
        }
        //Unselect options
        foreach ($isUnselect as $label) {
            if ($this->getChildElement($generalElement, sprintf($optionLocator, $label, 'input'))->selected()) {
                $this->getChildElement($generalElement, sprintf($optionLocator, $label, 'span'))->click();
            }
        }
        //Add new Options
        foreach ($isNeedAdd as $label) {
            if (!empty($label)) {
                $this->addCompositeMultiselectValue($fieldName, $label, $locator);
            }
        }
    }

    /**
     * Add value to CompositeMultiselect
     *
     * @param $fieldName
     * @param $fieldValue
     * @param null $locator
     * @param bool $failIfNotAdded
     *
     * @return bool
     * @throws RuntimeException
     */
    public function addCompositeMultiselectValue($fieldName, $fieldValue, $locator = null, $failIfNotAdded = true)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, $fieldName);
        }
        $generalElement = $this->getElement($locator);
        $saveValueWithoutForm = "//span[@title='Add']";
        $newButtonLocator = '//footer/span';
        $newValueLocator = "//input[@title='Enter new option']";
        $optionLocator = "//label[span='%s']/%s";
        //Define filling in type
        $this->getChildElement($generalElement, $newButtonLocator)->click();
        $newValueField = $this->elementIsPresent($locator . $newValueLocator);
        //Add new options
        if ($newValueField && $newValueField->enabled() && $newValueField->displayed()) {
            //by field
            $this->getChildElement($generalElement, $newValueLocator)->value($fieldValue);
            $this->getChildElement($generalElement, $saveValueWithoutForm)->click();
            $this->waitForElementOrAlert($locator . sprintf($optionLocator, $fieldValue, 'span'));
            if ($this->alertIsPresent()) {
                if ($failIfNotAdded) {
                    $this->fail("Value is not added to composite_multiselect field");
                }
                return false;
            }
        } else {
            //by new form
            throw new RuntimeException('@TODO fillCompositeMultiselect() - add new value in new form');
        }
        return true;
    }

    /**
     * Edit CompositeMultiselect Option
     *
     * @param string $fieldName
     * @param string $optionName
     * @param string|array $editData
     * @param null|string $locator
     * @throws RuntimeException
     */
    public function editCompositeMultiselectOption($fieldName, $optionName, $editData, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, $fieldName);
        }
        $labelLocator = "//div[normalize-space(label/span)='%s']";
        $generalElement = $this->getElement($locator);
        $optionElement = $this->getChildElement($generalElement, sprintf($labelLocator, $optionName));
        $this->moveto($optionElement);
        $optionElement->click();
        $this->getChildElement($optionElement, "//span[@title='Edit']")->click();
        $editOptionElement = $this->getChildElements($optionElement, '//input[@name="class_name"]', false);
        if (!empty($editOptionElement)) {
            /* @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            list($element) = $editOptionElement;
            $element->clear();
            $element->value($editData);
            $this->getChildElement($optionElement, '//button[@title="Save"]')->click();
            if ($this->alertIsPresent()) {
                $this->fail($this->alertText());
            }
            $this->waitForAjax();
            $this->getChildElement($generalElement, sprintf($labelLocator, $editData))->click();
        } else {
            //by edit form
            throw new RuntimeException('@TODO editCompositeMultiselectOption() - edit value in new form');
        }
    }

    /**
     * Delete CompositeMultiselect Option
     *
     * @param string $fieldName
     * @param string $optionName
     * @param string $message
     * @param null|string $locator
     */
    public function deleteCompositeMultiselectOption($fieldName, $optionName, $message, $locator = null)
    {
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, $fieldName);
        }
        $labelLocator = "//div[normalize-space(label/span)='$optionName']";
        $generalElement = $this->getElement($locator);
        $optionElement = $this->getChildElement($generalElement, $labelLocator);
        $optionElement->click();
        $this->getChildElement($optionElement, "//span[@title='Delete']")->click();
        $this->assertSame($this->_getMessageXpath($message), $this->alertText(), 'Confirmation massage is incorrect');
        $this->acceptAlert();
        if ($this->alertIsPresent()) {
            $this->fail($this->alertText());
        }
        $this->waitForAjax();
        $this->assertEmpty($this->getChildElements($generalElement, $labelLocator, false), 'Option is not deleted');
    }

    /**
     * Verify CompositeMultiselect
     *
     * @param string $fieldName
     * @param string|array $fieldValues
     * @param null|string $locator
     *
     * @return bool
     */
    public function verifyCompositeMultiselect($fieldName, $fieldValues, $locator = null)
    {
        //Prepare data
        if (is_string($fieldValues)) {
            $fieldValues = explode(',', $fieldValues);
        }
        $fieldValues = array_map('trim', $fieldValues);
        $fieldValues = array_diff($fieldValues, array(''));
        if (is_null($locator)) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, $fieldName);
        }

        $resultFlag = true;
        $actualValues = array();

        //Get selected options
        /* @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        $generalElement = $this->getElement($locator);
        foreach ($this->getChildElements($generalElement, '//div[label/span]/label') as $element) {
            if ($this->getChildElement($element, 'input')->selected()) {
                $actualValues[] = trim($element->text(), " \t\n\r\0\x0B");
            }
        }
        //Verify
        foreach ($fieldValues as $value) {
            if (!in_array($value, $actualValues)) {
                $actual = implode(', ', $actualValues);
                $this->addVerificationMessage(
                    "$fieldName: The value '$value' is not selected.(Selected values are: '$actual')"
                );
                $resultFlag = false;
            }
        }
        if (count($actualValues) != count($fieldValues)) {
            $actual = implode(', ', $actualValues);
            $expected = implode(', ', $fieldValues);
            $this->addVerificationMessage(
                "Amounts of the expected options are not equal to selected:('$expected' != '$actual')"
            );
            $resultFlag = false;
        }
        return $resultFlag;
    }

    ################################################################################
    #                                                                              #
    #                             Magento helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Waits for "Please wait" animated gif to appear and disappear.
     *
     * @param integer $waitDisappear Timeout in seconds to wait for the loader to disappear (by default = 30)
     *
     * @throws RuntimeException
     * @return Mage_Selenium_TestCase
     */
    public function pleaseWait($waitDisappear = null)
    {
        if ($this->controlIsVisible('message', 'general_js_error')) {
            $this->fail($this->locationToString() . 'JS error on page.');
        }
        $this->waitUntil(
            function ($testCase) {
                /** @var Mage_Selenium_TestCase $testCase */
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
                do {
                    $isLoaderDisplay = false;
                    $elements = $testCase->getControlElements('pageelement', 'loadingHolder');
                    foreach ($elements as $element) {
                        $isLoaderDisplay = $element->displayed() || $isLoaderDisplay;
                    }
                    $testCase->waitForAjax();
                } while ($isLoaderDisplay == true);
                return true;
            },
            $waitDisappear
        );
    }

    /**
     * Logs in as a default admin user on back-end
     * @return Mage_Selenium_TestCase
     */
    public function loginAdminUser()
    {
        $this->admin('log_in_to_admin', false);
        $loginData = array(
            'user_name' => $this->_configHelper->getDefaultLogin(),
            'password' => $this->_configHelper->getDefaultPassword()
        );
        if ($this->_findCurrentPageFromUrl() != $this->pageAfterAdminLogin) {
            $this->validatePage('log_in_to_admin');
            $this->fillFieldset($loginData, 'log_in');
            $this->clickButton('login', false);
            $this->waitForElement(array(
                $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'),
                $this->_getMessageXpath('general_error'),
                $this->_getMessageXpath('general_validation')
            ));
        }
        $this->checkCurrentPage($this->pageAfterAdminLogin);
        $this->assertEmptyVerificationErrors();
        $this->setCurrentPage($this->pageAfterAdminLogin);
        if ($this->pageAfterAdminLogin == 'store_launcher' &&
            $this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'welcome_popup')
        ) {
            $this->waitForControlStopsMoving(self::FIELD_TYPE_PAGEELEMENT, 'welcome_popup');
            $this->clickButton('back_to_storelauncher', false);
            $this->waitForControlNotVisible(self::FIELD_TYPE_PAGEELEMENT, 'welcome_popup');
        }
        $this->closeSystemMessagesDialog();
        if ($this->controlIsVisible('button', 'close_notification')) {
            $this->clickControl('button', 'close_notification', false);
        }
        return $this;
    }

    /**
     * Logs out from back-end
     * @return Mage_Selenium_TestCase
     */
    public function logoutAdminUser()
    {
        if ($this->getArea() !== 'admin') {
            $this->admin('log_in_to_admin', false);
        }
        $logOutLocator = $this->_getControlXpath('link', 'log_out');
        $adminUserLocator = $this->_getControlXpath('link', 'account_avatar');
        $availableElement = $this->elementIsPresent($adminUserLocator);
        if ($availableElement) {
            $this->focusOnElement($availableElement);
            $availableElement->click();
            $this->elementIsPresent($logOutLocator)->click();
            $this->waitForPageToLoad();
        }
        $this->validatePage('log_in_to_admin');

        return $this;
    }

    /**
     * Close System Messages Dialog
     */
    public function closeSystemMessagesDialog()
    {
        if ($this->getArea() == 'admin' &&
            $this->controlIsEditable(self::UIMAP_TYPE_FIELDSET, 'system_messages_list')
        ) {
            try {
                $this->clickControl('button', 'close_messages_list', false);
                $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'system_messages_list');
            } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            }
        }
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
        $this->navigate('cache_storage_management');
        $invalided = array('cache_disabled', 'cache_invalided');
        foreach ($invalided as $value) {
            $elements = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, $value, null, false);
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            foreach ($elements as $element) {
                $toSelect = $this->getChildElement($element, '//input');
                if (!$toSelect->selected()) {
                    $toSelect->click();
                }
            }
        }
        $selectedItems = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'selected_items', 'text');
        if ($selectedItems == 0) {
            return;
        }
        $this->fillDropdown('cache_action', 'Refresh');
        $this->addParameter('qtySelected', $selectedItems);
        $this->clickButton('submit', false);
        $this->waitForNewPage();
        $this->validatePage('cache_storage_management');
    }

    /**
     * Reindex indexes that are marked as 'reindex required' or 'update required'.
     */
    public function reindexInvalidedData()
    {
        $this->navigate('index_management');
        $invalided = array('reindex_required', 'update_required');
        foreach ($invalided as $value) {
            $locator = $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, $value);
            while ($this->elementIsPresent($locator)) {
                $this->getElement($locator . "//a[text()='Reindex Data']")->click();
                $this->waitForNewPage();
                $this->validatePage('index_management');
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
        $locator = $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'index_line');
        $count = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'index_line');
        for ($i = 1; $i <= $count; $i++) {
            $element = $this->getElement($locator . "[$i]");
            $name = trim($this->getChildElement($element, "td[$cellId]")->text());
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
                $this->waitForPageToLoad();
                $notLoaded = false;
            } catch (RuntimeException $e) {
                if ($retries == 10) {
                    throw new RuntimeException('Timed out after ' . ($this->_browserTimeout * 10) . ' seconds.');
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
        if ($this->getArea() !== 'frontend' || !preg_match('/^http/', $this->url())) {
            $this->frontend();
        }
        if ($this->controlIsPresent('link', 'log_out')) {
            //For GO default theme(Plushe). Open links menu
            if ($this->controlIsPresent(self::FIELD_TYPE_PAGEELEMENT, 'menu')) {
                $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'menu', false);
                $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'menu_active');
            }
            $this->clickControl('link', 'log_out', false);
            $this->waitForTextPresent('You are now logged out');
            $this->waitForTextNotPresent('You are now logged out');
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
        if ($this->controlIsPresent(self::FIELD_TYPE_DROPDOWN, 'your_language')) {
            $this->selectStoreScope(self::FIELD_TYPE_DROPDOWN, 'your_language', $storeViewName);
            return;
        }
        $this->addParameter('storeView', $storeViewName);
        $isSelectedLocator = $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'selected_store_view');
        $isSelected = $this->elementIsPresent($isSelectedLocator) ? true : false;
        if (!$isSelected) {
            $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'change_store_view', false);
            $this->waitForElementVisible($this->_getControlXpath('link', 'your_language'))->click();
            $isSelected = $this->elementIsPresent($isSelectedLocator) ? true : false;
        }
        $this->assertTrue($isSelected, 'Store view not changed to ' . $storeViewName);
    }

    /**
     * Select Store Scope
     *
     * @param string $controlType
     * @param string $controlName
     * @param null|string $scopePath
     * @param string $scopeType
     * @param bool $confirmation
     *
     * @throws OutOfRangeException
     * @return bool
     */
    public function selectStoreScope($controlType, $controlName, $scopePath = null,
                                     $confirmation = false, $scopeType = 'storeView')
    {
        if (is_null($scopePath)) {
            $scopePath = 'Main Website/Main Website Store/Default Store View';
        }
        //Define scope parameters
        $scopePath = explode('/', $scopePath);
        $method = 'fill' . ucfirst(strtolower($controlType));
        $locator = $this->_getControlXpath($controlType, $controlName);
        $element = $this->getElement($locator);
        switch ($scopeType) {
            case 'storeView':
                $countElements = count($scopePath);
                switch ($countElements) {
                    case 1:
                        list($storeView) = $scopePath;
                        $this->$method($controlName, $storeView);
                        break;
                    case 2:
                        //@TODO list($store, $storeView) = $scopePath;
                        break;
                    case 3:
                        //@TODO(if change -> test on manage_products page)
                        //and add page name
                        list($website, $store, $storeView) = $scopePath;
                        $websiteElement =
                            $element->element($this->using('xpath')->value("*[normalize-space(@label)='$website']"));
                        $storeElement = $websiteElement->element($this->using('xpath')
                            ->value("following-sibling::*[contains(@label,'$store')]"));
                        $storeViewElement = $storeElement->element($this->using('xpath')
                            ->value("option[contains(text(),'$storeView')]"));
                        $value = $storeViewElement->attribute('value');
                        $this->$method($controlName, $value, $locator, $confirmation);
                        break;
                }
                break;
            case 'store':
                //@TODO
                break;
            case 'website':
                //@TODO
                break;
            default:
                throw new OutOfRangeException('Wrong scope type');
                break;
        }
    }

    ################################################################################
    #                                                                              #
    #                               SELENIUM 2 FUNCTIONS                           #
    #                                                                              #
    ################################################################################
    /**
     * Get Locator Strategy
     *
     * @param $locator
     *
     * @return string
     */
    public function getLocatorStrategy(&$locator)
    {
        $locatorType = 'xpath';
        if (preg_match('/^css=/', $locator)) {
            $locatorType = 'css selector';
            $locator = str_replace('css=', '', $locator);
        } elseif (preg_match('/^id=/', $locator)) {
            $locatorType = 'id';
            $locator = str_replace('id=', '', $locator);
        } elseif (preg_match('/^name=/', $locator)) {
            $locatorType = 'name';
            $locator = str_replace('name=', '', $locator);
        } elseif (preg_match('/^class=/', $locator)) {
            $locatorType = 'class name';
            $locator = str_replace('class=', '', $locator);
        } elseif (preg_match('/^link=/', $locator)) {
            $locatorType = 'link text';
            $locator = str_replace('link=', '', $locator);
        }
        return $locatorType;
    }

    /**
     * Get elements
     *
     * @param string $locator
     * @param bool $failIfEmpty
     *
     * @return array
     */
    public function getElements($locator, $failIfEmpty = true)
    {
        $locatorType = $this->getLocatorStrategy($locator);
        try {
            $elements = $this->elements($this->using($locatorType)->value($locator));
        } catch (PHPUnit_Extensions_Selenium2TestCase_NoSeleniumException $e) {
            $this->markTestIncomplete($e->getMessage());
        }
        if (empty($elements) && $failIfEmpty) {
            $this->assertEmptyPageErrors();
            $this->fail($this->locationToString() . 'Element(s) with locator: "' . $locator
                . '" is not found on page');
        }
        return $elements;
    }

    /**
     * Get element
     *
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
     * Get child elements
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $parentElement
     * @param string $childLocator
     * @param bool $failIfEmpty
     *
     * @return array
     */
    public function getChildElements(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator,
                                     $failIfEmpty = true)
    {
        if (preg_match('|^//|', $childLocator)) {
            $childLocator = '.' . $childLocator;
        }
        $elements = $parentElement->elements($this->using('xpath')->value($childLocator));
        if (empty($elements) && $failIfEmpty) {
            $this->fail('Element(s) with locator: "' . $childLocator . '" is not found for parent element');
        }
        return $elements;
    }

    /**
     * Get child element
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $parentElement
     * @param string $childLocator
     *
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getChildElement(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
    {
        $elements = $this->getChildElements($parentElement, $childLocator);
        return array_shift($elements);
    }

    /**
     * Get control elements
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param mixed $uimap
     * @param bool $failIfEmpty
     *
     * @return array
     */
    public function getControlElements($controlType, $controlName, $uimap = null, $failIfEmpty = true)
    {
        $locator = $this->_getControlXpath($controlType, $controlName, $uimap);
        return $this->getElements($locator, $failIfEmpty);
    }

    /**
     * Get control element
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param mixed $uimap
     *
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getControlElement($controlType, $controlName, $uimap = null)
    {
        $elements = $this->getControlElements($controlType, $controlName, $uimap);
        return array_shift($elements);
    }

    /**
     * Verify if element is present on the page
     *
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
     * Verify if child element is present on the page
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $parentElement
     * @param string $childLocator
     *
     * @return PHPUnit_Extensions_Selenium2TestCase_Element|bool
     */
    public function childElementIsPresent(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
    {
        $childElements = $this->getChildElements($parentElement, $childLocator, false);
        return empty($childElements) ? false : array_shift($childElements);
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
     * Get child elements count
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $parentElement
     * @param string $childLocator
     *
     * @return int
     */
    public function getChildElementsCount(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
    {
        $childElements = $this->getChildElements($parentElement, $childLocator, false);
        return count($childElements);
    }

    /**
     * Waits for page to load
     *
     * @param null|integer $timeout
     *
     * @return bool
     * @throws RuntimeException
     */
    public function waitForPageToLoad($timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeout;
        }
        $jsCondition = "return document['readyState']";
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            usleep(500000);
            $result = $this->execute(array('script' => $jsCondition, 'args' => array()));
            if ($result === 'complete' || $result === 'uninitialized') {
                return true;
            }
        }
        throw new RuntimeException('Time is out for waitForPageToLoad');
    }

    /**
     * @return bool
     */
    public function alertIsPresent()
    {
        try {
            $this->alertText();
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * @param string $pageText
     *
     * @return bool
     */
    public function textIsPresent($pageText)
    {
        return (strpos($this->getElement('/*')->text(), $pageText) !== false);
    }

    /**
     * @param string $pageText
     * @param null $timeout
     */
    public function waitForTextPresent($pageText, $timeout = null)
    {
        $this->waitUntil(
            function ($testCase) use ($pageText) {
                /** @var Mage_Selenium_TestCase $testCase */
                if ($testCase->textIsPresent($pageText)) {
                    return true;
                }
            },
            $timeout
        );
    }

    /**
     * @param string $pageText
     * @param null $timeout
     */
    public function waitForTextNotPresent($pageText, $timeout = null)
    {
        $this->waitUntil(
            function ($testCase) use ($pageText) {
                /** @var Mage_Selenium_TestCase $testCase */
                if (!$testCase->textIsPresent($pageText)) {
                    return true;
                }
            },
            $timeout
        );
    }

    /**
     * Focus on element
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $element
     */
    public function focusOnElement(PHPUnit_Extensions_Selenium2TestCase_Element $element)
    {
        $elementId = $element->attribute('id');
        if ($elementId) {
            $script = 'Element.prototype.documentOffsetTop = function()'
                . '{return this.offsetTop + (this.offsetParent ? this.offsetParent.documentOffsetTop() : 0);};'
                . 'var element = document.getElementById("' . $elementId . '");'
                . 'var top = element.documentOffsetTop() - (window.innerHeight / 2);'
                . 'element.focus();window.scrollTo( 0, top );';
        } elseif ($element->attribute('name')) {
            $elementId = $element->attribute('name');
            $script = 'Element.prototype.documentOffsetTop = function()'
                . '{return this.offsetTop + (this.offsetParent ? this.offsetParent.documentOffsetTop() : 0);};'
                . 'var element = document.getElementsByName("' . $elementId . '");'
                . 'var top = element[0].documentOffsetTop() - (window.innerHeight / 2);'
                . 'element[0].focus();window.scrollTo( 0, top );';
        } else {
            return;
        }
        $this->execute(array('script' => $script, 'args' => array()));
    }

    /**
     * Clear active focus
     */
    public function clearActiveFocus()
    {
        $this->execute(array('script' => 'document.activeElement.blur()', 'args' => array()));
    }

    /**
     * Get suite
     *
     * @param string $className
     * @param Mage_Test_SkipFilter|null $filter
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite($className, $filter = null)
    {
        $class = new ReflectionClass($className);
        $staticProperties = $class->getStaticProperties();

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties['browsers'])) {
            $suite = new Mage_Selenium_TestSuite();
            if (null !== $filter) {
                $suite->setTestFilter($filter);
            }

            foreach ($staticProperties['browsers'] as $browser) {
                $browserSuite = PHPUnit_Extensions_SeleniumBrowserSuite::fromClassAndBrowser($className, $browser);
                foreach ($class->getMethods() as $method) {
                    $browserSuite->addTestMethod($class, $method);
                }
                $browserSuite->setupSpecificBrowser($browser);

                $suite->addTest($browserSuite);
            }
        } else {
            $suite = new Mage_Selenium_TestSuite($class, '', $filter);
        }
        return $suite;
    }

    /**
     * Run mass action on selected items
     *
     * Add following elements to UIMap with grid to get method work:
     * dropdowns: mass_action_select_action
     * buttons: submit
     * message: confirmation_for_delete
     * links: mass_action_select_all|mass_action_select_visible
     * See manage_products as example
     *
     * @param string $action Action to perform(From Actions dropdown)
     * @param string $select Specify 'all' or  only 'visible' items
     * @param string $message
     */
    public function runMassAction($action, $select = null, $message = 'confirmation_for_delete')
    {
        if ($this->controlIsVisible('message', 'no_records_found')) {
            return;
        }
        if ($select) {
            $this->clickControl(self::FIELD_TYPE_LINK, 'mass_action_select_' . strtolower($select), false);
        }
        $this->fillDropdown('mass_action_select_action', $action);
        $this->clickButtonAndConfirm('submit', $message);
    }

    /**
     * Wait for Visual JS effects to be finished
     *
     * @param $controlType
     * @param $controlName
     * @param int|null $timeout
     */
    public function waitForControlStopsMoving($controlType, $controlName, $timeout = null)
    {
        $element = $this->getControlElement($controlType, $controlName);
        $this->waitForElementStopsMoving($element, $timeout);
    }

    /**
     * Wait for Visual JS effects to be finished
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $element
     * @param int|null $timeout
     */
    public function waitForElementStopsMoving(PHPUnit_Extensions_Selenium2TestCase_Element $element, $timeout = null)
    {
        $this->waitUntil(
            function () use ($element) {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
                $elementLocation = $element->location();
                usleep(100000);
                if ($elementLocation == $element->location()) {
                    return true;
                }
            },
            $timeout
        );
    }

    /**
     * Get actual item order position
     *
     * @param $fieldType
     * @param $fieldName
     *
     * @return array
     */
    public function getActualItemOrder($fieldType, $fieldName)
    {
        $actualOrder = array();
        $blocks = $this->getControlElements($fieldType, $fieldName, null, false);
        $position = 1;
        /** @var $item PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($blocks as $item) {
            if ($fieldType == self::FIELD_TYPE_INPUT) {
                if($item->attribute('type') == 'hidden') {
                    preg_match_all('/\d+/', $item->attribute('name'), $matches);
                    $key = array_pop($matches[0]);
                } else {
                    $key = $item->value();
                }
            } else {
                $key = $item->text();
            }
            $actualOrder[$key] = $position++;
        }

        return $actualOrder;
    }

    /**
     * Reorder blocks according to desired position
     *
     * @param array $orderedBlocks
     * @param string $blockId
     * @param string $draggableElement
     * @param string $fieldType
     * @param string $fieldName
     */
    public function orderBlocks(array $orderedBlocks, $blockId, $draggableElement, $fieldType, $fieldName)
    {
        if (count($orderedBlocks) < 2) {
            return;
        }
        $actualOrder = $this->getActualItemOrder($fieldType, $fieldName);
        foreach ($orderedBlocks as $key => $value) {
            if (isset($actualOrder[$key]) && $value != $actualOrder[$key] && $value != 'noValue') {
                $this->addParameter($blockId, $key);
                $attributeBlock1 = $this->getControlElement(self::FIELD_TYPE_LINK, $draggableElement);
                $exchangeBlockName = array_search($value, $actualOrder);
                if ($exchangeBlockName === false) {
                    $this->fail('Block ' . $key . ' can not be moved to ' . $value . ' position');
                }
                $this->addParameter($blockId, array_search($value, $actualOrder));
                $attributeBlock2 = $this->getControlElement(self::FIELD_TYPE_LINK, $draggableElement);
                $locationBeforeMove = $attributeBlock2->location();
                $attempts = 2;
                while ($attempts > 0) {
                    $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
                    $this->moveto($attributeBlock1);
                    $this->buttondown();
                    $this->moveto($attributeBlock2);
                    $this->buttonup();
                    $locationAfterMove = $attributeBlock2->location();
                    $attempts--;
                    if ($locationBeforeMove['y'] != $locationAfterMove['y']) {
                        break;
                    } elseif ($attempts <= 0) {
                        $this->fail('Block position was not changed.');
                    }
                }
                $actualOrder = $this->getActualItemOrder($fieldType, $fieldName);
            }
        }
    }

    /**
     * Verify block sort order
     *
     * @param array $blockOrder
     * @param string $fieldType
     * @param string $fieldName
     */
    public function verifyBlocksOrder(array $blockOrder, $fieldType, $fieldName)
    {
        if (count($blockOrder) < 2) {
            return;
        }
        $actualOrder = array_keys($this->getActualItemOrder($fieldType, $fieldName));
        //Reorder item order considering duplication and empty position values
        $expectedOrder = array_keys($blockOrder);
        foreach ($blockOrder as $key => $value) {
            if ($value != 'noValue') {
                $keyToRemove = array_search($key, $expectedOrder);
                unset($expectedOrder[$keyToRemove]);
                if (isset($expectedOrder[$value - 1])) {
                    array_splice($expectedOrder, $value - 1, 0, $key);
                } else {
                    $expectedOrder[$value - 1] = $key;
                }
                ksort($expectedOrder);
            }
        }
        $this->assertEquals(array_values($expectedOrder), $actualOrder, 'Invalid block order');
    }

    /**
     * Wait till window will close
     *
     * @param int $countBeforeClose
     * @param int $timeout
     */
    public function waitForWindowToClose($countBeforeClose = 2, $timeout = null)
    {
        $this->waitUntil(function ($testCase) use ($countBeforeClose) {
            /** @var Mage_Selenium_TestCase $testCase */
            if (count($testCase->windowHandles()) == $countBeforeClose - 1) {
                $testCase->window('');
                return true;
            }
            sleep(1);
        }, $timeout);
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param $callback
     * @param null $timeout
     * @return mixed
     */
    public function waitUntil($callback, $timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeout;
        }
        $timeout = $timeout * 1000;
        return parent::waitUntil($callback, $timeout);
    }

    /**
     * Gets default browser timeout
     *
     * @return int
     */
    public function getBrowserTimeout()
    {
        return $this->_browserTimeout;
    }
}
