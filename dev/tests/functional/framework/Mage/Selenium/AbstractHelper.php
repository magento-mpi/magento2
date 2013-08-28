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
 * Abstract test helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method void                     _fill(array $fieldData)
 * @method string                   _findCurrentPageFromUrl(string $url = null)
 * @method Mage_Selenium_Uimap_Fieldset|Mage_Selenium_Uimap_Tab _findUimapElement(string $elementType, string $elementName, mixed $uimap = null)
 * @method Mage_Selenium_Uimap_Tab  _getActiveTabUimap()
 * @method string                   _getControlXpath(string $controlType, string $controlName, mixed $uimap = null)
 * @method array                    _getFormDataMap($fieldsets, $data)
 * @method string                   _getMessageXpath(string $message)
 * @method void                     _parseMessages()
 * @method array                    _prepareDataForSearch(array $data, array $checkFields = array('dropdown' => 'website'))
 * @method void                     addFieldIdToMessage(string $fieldType, string $fieldName)
 * @method void                     addMessage(string $type, $message)
 * @method Mage_Selenium_TestCase   addParameter(string $name, string $value)
 * @method void                     addVerificationMessage($message)
 * @method Mage_Selenium_TestCase   admin(string $page = null, bool $validatePage = true)
 * @method bool                     alertIsPresent()
 * @method Mage_Selenium_TestCase   appendParamsDecorator(Mage_Selenium_Helper_Params $paramsHelperObject)
 * @method void                     assertEmptyPageErrors()
 * @method void                     assertEmptyVerificationErrors()
 * @method void                     assertMessageNotPresent(string $type, string $message = null)
 * @method void                     assertMessagePresent(string $type, string $message = null)
 * @method void                     assertTrue(bool $condition, string $message = '')
 * @method void                     assertFalse(bool $condition, string $message = '')
 * @method bool                     buttonIsPresent(string $button)
 * @method bool                     checkCurrentPage(string $page)
 * @method array                    checkMessage(string $message)
 * @method array                    checkMessageByXpath(string $locator)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element|null childElementIsPresent(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
 * @method void                     clearActiveFocus()
 * @method array|bool               clearDataArray(array $dataArray)
 * @method void                     clearInvalidedCache()
 * @method void                     clearMessages(string $type = null)
 * @method void                     closeSystemMessagesDialog()
 * @method Mage_Selenium_TestCase   clickButton(string $button, bool $willChangePage = true)
 * @method void                     clickButtonAndConfirm(string $buttonName, string $message, bool $willChangePage = true)
 * @method Mage_Selenium_TestCase   clickControl(string $controlType, string $controlName, bool $willChangePage = true)
 * @method void                     clickControlAndConfirm(string $controlType, string $controlName, string $message, bool $willChangePage = true)
 * @method Mage_Selenium_TestCase   clickControlAndWaitMessage(string $controlType, string $controlName, bool $validate = true)
 * @method void                     closeLastWindow()
 * @method string                   combineLocatorsToOne(array $locators)
 * @method bool                     controlIsEditable(string $controlType, string $controlName)
 * @method bool                     controlIsPresent(string $controlType, string $controlName)
 * @method bool                     controlIsVisible(string $controlType, string $controlName)
 * @method null|string              defineIdFromTitle(string $locator)
 * @method null|string              defineIdFromUrl(string $url = null)
 * @method null|string              defineParameterFromUrl(string $paramName, string $url = null)
 * @method string                   detectOS()
 * @method bool|PHPUnit_Extensions_Selenium2TestCase_Element elementIsPresent(string $locator)
 * @method array                    errorMessage(string $message = null)
 * @method void                     fillCheckbox(string $name, string $value, string $locator = null)
 * @method void                     fillDropdown(string $name, string $value, string $locator = null)
 * @method void                     fillField(string $name, string $value, string $locator = null)
 * @method bool                     fillFieldset(array $data, string $fieldsetId, bool $failIfFieldsWithoutXpath = true)
 * @method void                     fillForm($data, $tabId = '')
 * @method void                     fillMultiselect(string $name, string $value, string $locator = null)
 * @method void                     fillRadiobutton(string $name, string $value, string $locator = null)
 * @method bool                     fillTab(array $data, string $tabId, bool $failIfFieldsWithoutXpath = true)
 * @method void                     flushCache()
 * @method void                     focusOnElement(PHPUnit_Extensions_Selenium2TestCase_Element $element)
 * @method string                   formSearchXpath(array $data)
 * @method Mage_Selenium_TestCase   frontend(string $page = 'home_page', bool $validatePage = true)
 * @method string                   generate(string $type = 'string', int $length = 100, bool $modifier = null, bool $prefix = null)
 * @method array                    getActualItemOrder($fieldType, $fieldName)
 * @method array                    getApplicationConfig()
 * @method string                   getArea()
 * @method array|string             getBasicXpathMessagesExcludeCurrent($types)
 * @method int                      getColumnIdByName(string $columnName, string $tableXpath = '//table[@id]')
 * @method Mage_Selenium_Helper_Config getConfigHelper()
 * @method string                   getControlAttribute(string $controlType, string $controlName, string $attribute)
 * @method int                      getControlCount(string $controlType, string $controlName, $locator = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element getControlElement($controlType, $controlName, $uimap = null)
 * @method array                    getControlElements($controlType, $controlName, $uimap = null, $failIfEmpty = true)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element getChildElement(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
 * @method array                    getChildElements(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator, $failIfEmpty = true)
 * @method int                      getChildElementsCount(PHPUnit_Extensions_Selenium2TestCase_Element $parentElement, $childLocator)
 * @method string                   getCurrentLocationArea()
 * @method Mage_Selenium_Uimap_Page getCurrentLocationUimapPage()
 * @method string                   getCurrentPage()
 * @method Mage_Selenium_Uimap_Page getCurrentUimapPage()
 * @method array                    getDataMapForFill(array $dataToFill, string $containerType, string $containerName)
 * @method string                   getDefaultScreenshotPath()
 * @method PHPUnit_Extensions_Selenium2TestCase_Element getElement(string $locator)
 * @method array                    getElements(string $locator, bool $failIfEmpty = true)
 * @method array                    getElementsValue(string $locator, string $getCommand, string $getParameter = null)
 * @method mixed                    getFile(string $url)
 * @method array                    getHttpResponse(string $url)
 * @method string                   getLocatorStrategy(string $locator)
 * @method string                   getMcaFromUrl(string $url = null)
 * @method array                    getMessagesOnPage(string $messageType = null)
 * @method string                   getPageUrl(string $area, string $page, Mage_Selenium_Helper_Params $paramsDecorator = null)
 * @method string                   getParameter(string $name)
 * @method Mage_Selenium_Helper_Params getParamsHelper()
 * @method array                    getParsedMessages(string $type = null)
 * @method string                   getScreenshotPath()
 * @method array                    getTableHeadRowNames(string $tableLocator = '//table[@id]')
 * @method string                   getTotalRecordsInTable(string $controlType, string $controlName)
 * @method Mage_Selenium_Uimap_Page getUimapPage(string $area, string $pageKey)
 * @method string                   getUrlPrefix(string $area = 'frontend')
 * @method Mage_Selenium_TestCase   goToArea(string $area = 'frontend', string $page = '', bool $validatePage = true)
 * @method bool                     isControlExpanded(string $controlType, string $controlName)
 * @method Mage_Selenium_TestCase   helper(string $className)
 * @method bool                     httpResponseIsOK(string $url)
 * @method array                    loadDataSet(string $dataFile, string $dataSource, $overrideByKey = null, $overrideByValueParam = null)
 * @method Mage_Selenium_TestCase   loginAdminUser()
 * @method Mage_Selenium_TestCase   logoutAdminUser()
 * @method Mage_Selenium_TestCase   logoutCustomer()
 * @method string                   locationToString()
 * @method array                    messagesToString($message)
 * @method Mage_Selenium_TestCase   navigate(string $page, bool $validatePage = true)
 * @method void                     openTab(string $tabName)
 * @method void                     orderBlocks(array $orderedBlocks, $blockId, $draggableElement, $fieldType, $fieldName)
 * @method array                    overrideArrayData(array $dataForOverride, array $overrideArray, string $overrideType)
 * @method bool                     overrideDataByCondition(string $overrideKey, string $overrideValue, array &$overrideArray, string $condition)
 * @method array                    parseValidationMessages()
 * @method Mage_Selenium_TestCase   pleaseWait(int $waitDisappear = 30)
 * @method void                     reindexAllData()
 * @method void                     reindexInvalidedData()
 * @method void                     saveAndContinueEdit(string $controlType, string $controlName)
 * @method Mage_Selenium_TestCase   saveForm(string $buttonName, bool $validate = true)
 * @method string                   saveHtmlPage(string $fileName = null)
 * @method string                   search(array $data, string $fieldsetName)
 * @method void                     searchAndChoose(array $data, string $fieldSetName)
 * @method void                     searchAndOpen(array $data, string $fieldSetName, bool $willChangePage = true)
 * @method void                     selectFrontStoreView(string $storeViewName = 'Default Store View')
 * @method string                   selectLastWindow()
 * @method bool                     selectStoreScope(string $controlType, string $controlName, string $scopePath = null, string $scopeType = 'storeView')
 * @method Mage_Selenium_TestCase   setArea(string $name)
 * @method Mage_Selenium_TestCase   setCurrentPage(string $page)
 * @method void                     setDataParams(string &$value)
 * @method Mage_Selenium_TestCase   setDefaultScreenshotPath(string $path)
 * @method Mage_Selenium_TestCase   setScreenshotPath(string $path)
 * @method void                     setUrlPostfix(string $params)
 * @method void                     skipTestWithScreenshot(string $message)
 * @method array                    successMessage(string $message = null)
 * @method string                   takeScreenshot(string $fileName = null)
 * @method bool                     textIsPresent(string $pageText)
 * @method array                    fixtureDataToArray($testData)
 * @method void                     verifyBlocksOrder(array $blockOrder, $fieldType, $fieldName)
 * @method void                     validatePage(string $page = '')
 * @method array                    validationMessage(string $message = null)
 * @method bool                     verifyForm(array $data, string $tabId = '', array $skipElements = array('password', 'password_confirmation'))
 * @method bool                     verifyMessagesCount(int $count = 1, string $locator = null)
 * @method void                     waitForAjax(int $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForElement($locator, int $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForElementEditable($locator, int $timeout = null)
 * @method bool                     waitForElementOrAlert($locator, int $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForElementVisible($locator, int $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForElementNotVisible($locator, int $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForControl($controlType, $controlName, $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForControlEditable($controlType, $controlName, $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForControlVisible($controlType, $controlName, $timeout = null)
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForControlNotVisible($controlType, $controlName, $timeout = null)
 * @method void                     waitForWindowToClose()
 * @method void                     waitForNewPage()
 * @method bool                     waitForPageToLoad()
 * @method void                     waitForTextNotPresent(int $pageText, int $timeout = null)
 * @method void                     waitForTextPresent(int $pageText, int $timeout = null)
 * @method int                      getBrowserTimeout()
 *
 * @method Core_Mage_AdminUser_Helper                                                                  adminUserHelper()
 * @method Core_Mage_AdvancedSearch_Helper                                                             advancedSearchHelper()
 * @method Core_Mage_AttributeSet_Helper                                                               attributeSetHelper()
 * @method Core_Mage_Category_Helper|Enterprise_Mage_Category_Helper                                   categoryHelper()
 * @method Core_Mage_CheckoutMultipleAddresses_Helper|Enterprise_Mage_CheckoutMultipleAddresses_Helper checkoutMultipleAddressesHelper()
 * @method Core_Mage_CheckoutOnePage_Helper|Enterprise_Mage_CheckoutOnePage_Helper                     checkoutOnePageHelper()
 * @method Core_Mage_CmsPages_Helper                                                                   cmsPagesHelper()
 * @method Core_Mage_CmsPolls_Helper                                                                   cmsPollsHelper()
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
 *
 * @method fail($message)
 * @method assertEquals(mixed $expected, mixed $actual, string $message = '', float $delta = 0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method assertNotEquals(mixed $expected, mixed $actual, string $message = '', float $delta = 0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
 * @method assertSame(mixed $expected, mixed $actual, string $message = '')
 * @method assertNotNull(mixed $actual, string $message = '')
 * @method assertNull(mixed $actual, string $message = '')*
 *
 * @method void         acceptAlert() Press OK on an alert, or confirms a dialog
 * @method mixed        alertText($value = null) Gets the alert dialog text, or sets the text for a prompt dialog
 * @method void         buttondown()
 * @method void         buttonup()
 * @method void         clickOnElement($id)
 * @method void         closeWindow()
 * @method void         dismissAlert() Press Cancel on an alert, or does not confirm a dialog
 * @method string       execute($javaScriptCode) Injects arbitrary JavaScript in the page and returns the last
 * @method void         frame($elementId) Changes the focus to a frame in the page
 * @method void         moveto(PHPUnit_Extensions_Selenium2TestCase_Element $element)
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element_Select select($element)
 * @method void         window($name) Changes the focus to another window
 * @method string       windowHandles() Retrieves a list of all available window handles
 * @method void|string  url($url = null)
 * @method void         markTestIncomplete($message)
 * @method PHPUnit_Extensions_Selenium2TestCase_Session_Cookie cookie()
 * @method mixed        waitUntil($callback, $timeout = null)
 * @method void         refresh()
 */
//@codingStandardsIgnoreEnd
class Mage_Selenium_AbstractHelper
{
    /**
     * Test object
     * @var Mage_Selenium_TestCase
     */
    protected $_testInstance;

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

    /**
     * Constructor expects global test object
     *
     * @param  Mage_Selenium_TestCase $testObject
     */
    public function __construct(Mage_Selenium_TestCase $testObject)
    {
        $this->_testInstance = $testObject;
        $this->_init();
    }

    /**
     * @return Mage_Selenium_AbstractHelper
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Delegate method calls to Mage_Selenium_TestCase class.
     *
     * @param string $command   Command (method) name to call
     * @param array  $arguments Arguments to be sent to the called command (method)
     *
     * @return mixed
     */
    public function __call($command, $arguments)
    {
        $className = get_class($this->_testInstance);
        $reflectionClass = new ReflectionClass($className);
        if ($reflectionClass->hasMethod($command)) {
            $reflectionMethod = new ReflectionMethod($className, $command);

            return $reflectionMethod->invokeArgs($this->_testInstance, $arguments);
        }

        return $this->_testInstance->__call($command, $arguments);
    }

    /**
     * Return Test Instance
     * @return Mage_Selenium_TestCase
     */
    public function getTestInstance()
    {
        return $this->_testInstance;
    }

    /**
     * Set Test Instance
     */
    public function setTestInstance(Mage_Selenium_TestCase $testInstance)
    {
        $this->_testInstance = $testInstance;
    }
}
