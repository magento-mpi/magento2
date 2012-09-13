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
 * Abstract test helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method helper()
 * @method skipTestWithScreenshot()
 * @method assertTrue()
 * @method assertFalse()
 * @method appendParamsDecorator()
 * @method addParameter()
 * @method getParameter()
 * @method defineParameterFromUrl()
 * @method defineIdFromTitle()
 * @method defineIdFromUrl()
 * @method addFieldIdToMessage()
 * @method generate()
 * @method loadDataSet()
 * @method overrideArrayData()
 * @method overrideDataByCondition()
 * @method setDataParams()
 * @method clearDataArray()
 * @method clearMessages()
 * @method getMessagesOnPage()
 * @method getParsedMessages()
 * @method addMessage()
 * @method addVerificationMessage()
 * @method verifyMessagesCount()
 * @method checkMessage()
 * @method checkMessageByXpath()
 * @method errorMessage()
 * @method successMessage()
 * @method validationMessage()
 * @method assertMessagePresent()
 * @method assertMessageNotPresent()
 * @method assertEmptyVerificationErrors()
 * @method setUrlPostfix()
 * @method goToArea()
 * @method navigate()
 * @method admin()
 * @method frontend()
 * @method selectLastWindow()
 * @method closeLastWindow()
 * @method getCurrentLocationArea()
 * @method setArea()
 * @method getArea()
 * @method getApplicationConfig()
 * @method Mage_Selenium_Uimap_Page getUimapPage()
 * @method Mage_Selenium_Uimap_Page getCurrentUimapPage()
 * @method Mage_Selenium_Uimap_Page getCurrentLocationUimapPage()
 * @method setCurrentPage()
 * @method getCurrentPage()
 * @method checkCurrentPage()
 * @method validatePage()
 * @method getPageUrl()
 * @method getControlAttribute()
 * @method getHttpResponse()
 * @method httpResponseIsOK()
 * @method saveHtmlPage()
 * @method takeScreenshot()
 * @method detectOS()
 * @method getScreenshotPath()
 * @method setScreenshotPath()
 * @method setDefaultScreenshotPath()
 * @method getDefaultScreenshotPath()
 * @method clickControl()
 * @method clickButton()
 * @method clickControlAndConfirm()
 * @method clickButtonAndConfirm()
 * @method controlIsPresent()
 * @method controlIsVisible()
 * @method controlIsEditable()
 * @method buttonIsPresent()
 * @method openTab()
 * @method getControlCount()
 * @method getTableHeadRowNames()
 * @method getColumnIdByName()
 * @method combineLocatorsToOne()
 * @method PHPUnit_Extensions_Selenium2TestCase_Element waitForElement()
 * @method waitForElementOrAlert()
 * @method waitForElementVisible()
 * @method waitForElementEditable()
 * @method waitForAjax()
 * @method saveAndContinueEdit()
 * @method saveForm()
 * @method clickControlAndWaitMessage()
 * @method getBasicXpathMessagesExcludeCurrent()
 * @method searchAndOpen()
 * @method searchAndChoose()
 * @method search()
 * @method formSearchXpath()
 * @method fillFieldset()
 * @method fillTab()
 * @method fillForm()
 * @method verifyForm()
 * @method pleaseWait()
 * @method loginAdminUser()
 * @method logoutAdminUser()
 * @method flushCache()
 * @method clearInvalidedCache()
 * @method reindexInvalidedData()
 * @method reindexAllData()
 * @method waitForNewPage()
 * @method logoutCustomer()
 * @method selectFrontStoreView()
 * @method selectStoreScope()
 * @method getLocatorStrategy()
 * @method getElements()
 * @method PHPUnit_Extensions_Selenium2TestCase_Element getElement()
 * @method PHPUnit_Extensions_Selenium2TestCase_Element elementIsPresent()
 * @method getElementsValue()
 * @method waitForPageToLoad()
 * @method alertIsPresent()
 * @method textIsPresent()
 * @method waitForTextPresent()
 * @method waitForTextNotPresent()
 * @method focusOnElement()
 * @method clearActiveFocus()
 *
 * @method _fill()
 * @method fillField()
 * @method fillMultiselect()
 * @method fillDropdown()
 * @method fillCheckbox()
 * @method fillRadiobutton()
 * @method _getControlXpath()
 * @method _getMessageXpath()
 * @method Mage_Selenium_Uimap_Fieldset|Mage_Selenium_Uimap_Tab _findUimapElement()
 * @method _parseMessages()
 * @method _getFormDataMap()
 * @method getDataMapForFill()
 * @method _prepareDataForSearch()
 * @method _findCurrentPageFromUrl()
 * @method _getActiveTabUimap()
 * @method getMcaFromUrl()
 * @method Mage_Selenium_Helper_Config getConfigHelper()
 * @method getParamsHelper()
 *
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
 *
 * @method fail()
 * @method assertEquals()
 * @method assertNotEquals()
 * @method assertSame()
 * @method assertNotNull()
 *
 * @method void acceptAlert() Press OK on an alert, or confirms a dialog
 * @method mixed alertText($value = NULL) Gets the alert dialog text, or sets the text for a prompt dialog
 * @method void back()
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element byCssSelector($value)
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element byClassName($vaue)
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element byId($value)
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element byName($value)
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element byXPath($value)
 * @method void clickOnElement($id)
 * @method string currentScreenshot() BLOB of the image file
 * @method void dismissAlert() Press Cancel on an alert, or does not confirm a dialog
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element element(\PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria) Retrieves an element
 * @method array elements(\PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria) Retrieves an array of Element instances
 * @method string execute($javaScriptCode) Injects arbitrary JavaScript in the page and returns the last
 * @method string executeAsync($javaScriptCode) Injects arbitrary JavaScript and wait for the callback (last element of arguments) to be called
 * @method void forward()
 * @method void frame($elementId) Changes the focus to a frame in the page
 * @method void refresh()
 * @method \PHPUnit_Extensions_Selenium2TestCase_Element_Select select($element)
 * @method string source() Returns the HTML source of the page
 * @method \PHPUnit_Extensions_Selenium2TestCase_Session_Timeouts timeouts()
 * @method string title()
 * @method void|string url($url = NULL)
 * @method PHPUnit_Extensions_Selenium2TestCase_ElementCriteria using($strategy) Factory Method for Criteria objects
 * @method void window($name) Changes the focus to another window
 * @method string windowHandle() Retrieves the current window handle
 * @method string windowHandles() Retrieves a list of all available window handles
 * @method string keys() Send a sequence of key strokes to the active element.
 * @method moveto()
 * @method buttondown()
 * @method buttonup()
 * @method closeWindow()
 */
class Mage_Selenium_AbstractHelper
{
    /**
     * Test object
     * @var Mage_Selenium_TestCase
     */
    protected $_testInstance;

    /**
     * Constructor expects global test object
     *
     * @param  Mage_Selenium_TestCase $testObject
     */
    public function  __construct(Mage_Selenium_TestCase $testObject)
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
     * @param string $command Command (method) name to call
     * @param array $arguments Arguments to be sent to the called command (method)
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
     * Return config
     * @return Mage_Selenium_TestCase
     */
    public function getConfig()
    {
        return $this->_testInstance;
    }
}
