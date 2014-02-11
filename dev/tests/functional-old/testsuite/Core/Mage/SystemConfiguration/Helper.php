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
                $waitConditions[] = $this->_getControlXpath(
                    'field', 'user_name', $this->getUimapPage('admin', 'log_in_to_admin')
                );
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
        $messages = $this->getParsedMessages('verification');
        if ($messages) {
            $this->clearMessages('verification');
            $skipError = preg_quote('" != "******")');
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
        $this->assertMessagePresent('success', 'success_saved_config');
        $this->assertTrue($this->verifyForm($data, 'general_web'), $this->getParsedMessages());
    }

    /**
     * PayPal System Configuration
     *
     * @param array|string $parameters
     *
     * @throws RuntimeException
     */
    public function configurePaypal($parameters)
    {
        $parameters = $this->fixtureDataToArray($parameters);
        $configuration = (isset($parameters['configuration'])) ? $parameters['configuration'] : array();
        if (isset($parameters['configuration_scope']) &&
            $this->controlIsVisible('dropdown', 'current_configuration_scope')
        ) {
            $this->selectStoreScope('dropdown', 'current_configuration_scope', $parameters['configuration_scope']);
        }
        $this->openConfigurationTab('sales_payment_methods');
        $this->disableAllPaypalMethods();
        foreach ($configuration as &$payment) {
            if (!isset($payment['payment_name']) || !isset($payment['general_fieldset'])) {
                throw new RuntimeException('Required parameter "payment_name"(or "general_fieldset") is not set');
            }
            $this->disclosePaypalFieldset($payment['general_fieldset']);
            if ($this->controlIsVisible('button', $payment['payment_name'] . '_configure')) {
                $this->clickButton($payment['payment_name'] . '_configure', false);
            }
            foreach ($payment as &$dataSet) {
                if (!is_array($dataSet)) {
                    continue;
                }
                $fieldsetName = $this->disclosePaypalFieldset($dataSet['path']);
                foreach ($dataSet['data'] as $key => $value) {
                    $dataSet['data'][$payment['payment_name'] . '_' . $key] = $value;
                    unset($dataSet['data'][$key]);
                }
                $this->fillFieldset($dataSet['data'], $fieldsetName);
            }
        }
        $this->saveForm('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * @param array $configuration
     */
    public function verifyPaypalSettings(array $configuration)
    {
        foreach ($configuration as $payment) {
            $this->disclosePaypalFieldset($payment['general_fieldset']);
            if ($this->controlIsVisible('button', $payment['payment_name'] . '_configure')) {
                $this->clickButton($payment['payment_name'] . '_configure', false);
            }
            foreach ($payment as $dataSet) {
                if (is_array($dataSet)) {
                    $this->disclosePaypalFieldset($dataSet['path']);
                    $this->verifyForm($dataSet['data'], 'sales_payment_methods');
                }
            }
        }
        $messages = $this->getParsedMessages('verification');
        if ($messages) {
            $this->clearMessages('verification');
            $skipError = preg_quote('" != "******")');
            foreach ($messages as $errorMessage) {
                if (!preg_match('#' . $skipError . '#i', $errorMessage)) {
                    $this->addVerificationMessage($errorMessage);
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verify Tab Fields Availability
     */
    public function verifyTabFieldsAvailability()
    {
        $openedTabs = 0;
        $tabsCount = $this->getControlCount('tab', 'all_tabs') - 1;
        /** @var $tabUimap Mage_Selenium_Uimap_Tab */
        foreach ($this->getCurrentUimapPage()->getMainForm()->getAllTabs() as $tabName => $tabUimap) {
            if ($tabName == 'all_tabs' || $tabName == 'sales_payment_methods') {
                continue;
            }
            $this->openConfigurationTab($tabName);
            $openedTabs++;
            $openedFieldsets = 0;
            $fieldsetCount = $this->getControlCount('fieldset', 'all_fieldsets');
            /** @var $fieldsetUimap Mage_Selenium_Uimap_Fieldset */
            foreach ($tabUimap->getAllFieldsets() as $fieldsetName => $fieldsetUimap) {
                $this->expandFieldSet($fieldsetName);
                foreach ($fieldsetUimap->getFieldsetElements() as $fieldType => $fieldsData) {
                    foreach ($fieldsData as $fieldName => $fieldLocator) {
                        if (in_array($fieldName, array('store_state_region', 'origin_region'))
                            || preg_match('/%\w+%/', $fieldLocator)
                        ) {
                            continue;
                        }
                        if (!$this->elementIsPresent($fieldLocator)) {
                            $this->addVerificationMessage(
                                sprintf('%s tab: "%s" %s is not visible on the page', $tabName, $fieldName, $fieldType)
                            );
                        }
                    }
                }
                $openedFieldsets++;
            }
            if ($fieldsetCount != $openedFieldsets) {
                $this->addVerificationMessage(
                    sprintf(
                        'There are more fieldsets on "%s" tab then defined(%s != %s)',
                        $tabName,
                        $fieldsetCount,
                        $openedFieldsets
                    )
                );
            }
        }
        if ($tabsCount != $openedTabs) {
            $this->addVerificationMessage(
                sprintf('There are more tabs then defined(%s != %s)', $openedTabs, $tabsCount)
            );
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Disable all active paypal payment methods
     *
     * @return null
     */
    public function disableAllPaypalMethods()
    {
        if (!$this->controlIsPresent('button', 'active_paypal_method')) {
            return;
        }
        $closePaypalFieldsetButtons = array();
        foreach ($this->_getActiveTabUimap()->getAllButtons() as $key => $value) {
            if (preg_match('/_close$/', $key)) {
                $closePaypalFieldsetButtons[preg_replace('/_close$/', '', $key)] = $value;
            }
        }
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        foreach ($this->getControlElements('button', 'active_paypal_method') as $element) {
            $idRegExp = preg_quote('@id=\'' . $element->attribute('id'));
            foreach ($closePaypalFieldsetButtons as $name => $locator) {
                if (preg_match('/' . $idRegExp . '/', $locator)) {
                    $this->moveto($element);
                    $element->click();
                    if ($this->controlIsEditable('dropdown', $name . '_enable')) {
                        $this->fillDropdown($name . '_enable', 'No');
                    }
                    unset($closePaypalFieldsetButtons[$name]);
                    break;
                }
            }
        }
    }

    /**
     * Disclose Paypal fieldset
     *
     * @param string $path
     *
     * @return string Fieldset name for filling in
     */
    public function disclosePaypalFieldset($path)
    {
        $fullPath = explode('/', $path);
        $fullPath = array_map('trim', $fullPath);
        foreach ($fullPath as $node) {
            $class = $this->getControlAttribute('fieldset', $node, 'class');
            if (!preg_match('/active/', $class)) {
                $this->clickControl('link', $node . '_section', false);
            }
        }

        return end($fullPath);
    }
}