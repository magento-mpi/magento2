<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_SystemConfiguration
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_SystemConfiguration_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * System Configuration
     *
     * @param array|string $parameters
     */
    public function configure($parameters)
    {
        $parameters = $this->fixtureDataToArray($parameters);
        if (isset($parameters['configuration_scope']) &&
            $this->controlIsVisible('dropdown', 'current_configuration_scope')
        ) {
            $this->selectStoreScope('dropdown', 'current_configuration_scope', $parameters['configuration_scope']);
        }
        foreach ($parameters as $value) {
            if (!is_array($value) || !isset($value['tab_name']) || !isset($value['configuration'])) {
                continue;
            }
            $this->openConfigurationTab($value['tab_name']);
            $possibleLogOut = preg_match('/^advanced_/', $value['tab_name']);
            foreach ($value['configuration'] as $fieldsetName => $fieldsetData) {
                $this->expandFieldSet($fieldsetName);
                $this->fillFieldset($fieldsetData, $fieldsetName);
            }
            $waitConditions = $this->getBasicXpathMessagesExcludeCurrent(array('success', 'error', 'validation'));
            if ($possibleLogOut) {
                $waitConditions[] = $this->_getControlXpath('field', 'user_name',
                    $this->getUimapPage('admin', 'log_in_to_admin'));
            }
            $this->clickButton('save_config', false);
            $this->waitForElementVisible($waitConditions);
            $this->validatePage();
            if ($possibleLogOut && $this->getCurrentPage() == 'log_in_to_admin') {
                if ($this->controlIsVisible('field', 'captcha')) {
                    return;
                }
                $this->loginAdminUser();
                $this->navigate('system_configuration');
                $this->openConfigurationTab($value['tab_name']);
            } else {
                $this->assertMessagePresent('success', 'success_saved_config');
            }
            $this->verifyConfigurationOptions($value['configuration'], $value['tab_name']);
        }
    }

    /**
     * @param string $fieldsetName
     */
    public function expandFieldSet($fieldsetName)
    {
        $formLocator = $this->getControlElement('fieldset', $fieldsetName);
        if ($formLocator->name() != 'fieldset') {
            return;
        }
        if (!$formLocator->displayed()) {
            $fieldsetLink = $this->getControlElement('link', $fieldsetName . '_link');
            $this->focusOnElement($fieldsetLink);
            $fieldsetLink->click();
            $this->clearActiveFocus();
            if (!$formLocator->displayed()) {
                $this->fail('Could not expand System Configuration section');
            }
        }
    }

    /**
     * @param array $tabSettings
     * @param string $tab
     */
    public function verifyConfigurationOptions(array $tabSettings, $tab)
    {
        foreach ($tabSettings as $fieldsetName => $fieldsetData) {
            $this->expandFieldSet($fieldsetName);
            $this->verifyForm($fieldsetData, $tab);
        }
        if ($this->getParsedMessages('verification')) {
            $messages = $this->getParsedMessages('verification');
            $this->clearMessages('verification');
            $skipError = preg_quote("' != '**");
            foreach ($messages as $errorMessage) {
                if (!preg_match('#' . $skipError . '#i', $errorMessage)) {
                    $this->addVerificationMessage($errorMessage);
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Open tab on Configuration page
     *
     * @param string $tab
     */
    public function openConfigurationTab($tab)
    {
        if (!$this->controlIsPresent('tab', $tab)) {
            $this->fail($this->locationToString() . "Tab '$tab' is not present on the page");
        }
        $this->defineParameters('tab', $tab, 'href');
        $url = $this->getControlElement('tab', $tab)->attribute('href');
        $this->url($url);
        $this->validatePage();
    }

    /**
     * Define Url Parameters for System Configuration page
     *
     * @param string $controlType
     * @param string $controlName
     * @param string $attribute
     *
     * @return void
     */
    public function defineParameters($controlType, $controlName, $attribute)
    {
        $params = $this->getControlAttribute($controlType, $controlName, $attribute);
        $params = explode('/', $params);
        foreach ($params as $key => $value) {
            if ($value == 'section' && isset($params[$key + 1])) {
                $this->addParameter('tabName', $params[$key + 1]);
            }
            if ($value == 'website' && isset($params[$key + 1])) {
                $this->addParameter('webSite', $params[$key + 1]);
            }
            if ($value == 'store' && isset($params[$key + 1])) {
                $this->addParameter('storeName', $params[$key + 1]);
            }
        }
    }

    /**
     * Enable/Disable option 'Use Secure URLs in Admin/Frontend'
     *
     * @param string $path
     * @param string $useSecure
     */
    public function useHttps($path = 'admin', $useSecure = 'Yes')
    {
        $this->admin('system_configuration');
        $this->openConfigurationTab('general_web');
        $this->expandFieldSet('secure');
        $secureBaseUrl = $this->getControlAttribute('field', 'secure_base_url', 'value');
        $data = array(
            'secure_base_url' => preg_replace('/http(s)?/', 'https', $secureBaseUrl),
            'use_secure_urls_in_' . $path => ucwords(strtolower($useSecure))
        );
        $this->fillFieldset($data, 'secure');
        $this->clickButton('save_config');
        $this->assertTrue($this->verifyForm($data, 'general_web'), $this->getParsedMessages());
    }

    /**
     * @param $parameters
     */
    public function configurePaypal($parameters)
    {
        $this->configure($parameters);
    }

    /**
     * @param string $tabName
     */
    public function verifyTabFieldsAvailability($tabName)
    {
        $needFieldTypes = array('multiselect', 'dropdown', 'field');
        $tabUimap = $this->_findUimapElement('tab', $tabName);
        $this->systemConfigurationHelper()->openConfigurationTab($tabName);
        $uimapFields = $tabUimap->getTabElements($this->getParamsHelper());
        $storeView = $this->_getControlXpath('pageelement', 'store_view_hint');
        $globalView = $this->_getControlXpath('pageelement', 'global_view_hint');
        $websiteView = $this->_getControlXpath('pageelement', 'website_view_hint');
        foreach ($uimapFields as $fieldType => $fieldTypeData) {
            if (!in_array($fieldType, $needFieldTypes)) {
                continue;
            }
            foreach ($fieldTypeData as $fieldName => $fieldLocator) {
                if (!$this->elementIsPresent($fieldLocator)) {
                    $this->addVerificationMessage("Element $fieldName with locator $fieldLocator is not on the page");
                    continue;
                }
                if (!$this->elementIsPresent($fieldLocator . $globalView)
                    && !$this->elementIsPresent($fieldLocator . $websiteView)
                    && !$this->elementIsPresent($fieldLocator . $storeView)
                ) {
                    $this->addVerificationMessage('Scope label for element "' . $fieldName . '" with locator "'
                        . $fieldLocator . '" is not on the page');
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}