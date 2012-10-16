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
class Core_Mage_SystemConfiguration_Helper extends Mage_Selenium_TestCase
{
    /**
     * System Configuration
     *
     * @param array|string $parameters
     */
    public function configure($parameters)
    {
        if (is_string($parameters)) {
            $elements = explode('/', $parameters);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $parameters = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $chooseScope = (isset($parameters['configuration_scope'])) ? $parameters['configuration_scope'] : null;
        if ($chooseScope) {
            $this->changeConfigurationScope('current_configuration_scope', $chooseScope);
        }
        foreach ($parameters as $value) {
            if (!is_array($value)) {
                continue;
            }
            $tab = (isset($value['tab_name'])) ? $value['tab_name'] : null;
            $settings = (isset($value['configuration'])) ? $value['configuration'] : null;
            if ($tab) {
                $this->openConfigurationTab($tab);
                $this->fillForm($settings, $tab);
                $this->saveForm('save_config');
                $this->assertMessagePresent('success', 'success_saved_config');
                $this->verifyForm($settings, $tab);
                if ($this->getParsedMessages('verification')) {
                    foreach ($this->getParsedMessages('verification') as $key => $errorMessage) {
                        if (preg_match('#(\'all\' \!\=)|(\!\= \'\*\*)|(\'all\')#i', $errorMessage)) {
                            unset(self::$_messages['verification'][$key]);
                        }
                    }
                    $this->assertEmptyVerificationErrors();
                }
            }
        }
    }

    /**
     * Open tab on Configuration page
     *
     * @param string $tab
     */
    public function openConfigurationTab($tab)
    {
        $this->defineParameters($this->_getControlXpath('tab', $tab), 'href');
        $this->clickControl('tab', $tab);
    }

    /**
     * @param string $dropDownName
     * @param string $fieldValue
     */
    public function changeConfigurationScope($dropDownName, $fieldValue)
    {
        $xpath = $this->_getControlXpath('dropdown', $dropDownName);
        $toSelect = $xpath . '//option[normalize-space(text())="' . $fieldValue . '"]';
        $isSelected = $toSelect . '[@selected]';
        if (!$this->isElementPresent($isSelected)) {
            $this->defineParameters($toSelect, 'url');
            $this->fillDropdown($dropDownName, $fieldValue);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage();
        }
    }

    /**
     * Define Url Parameters for System Configuration page
     *
     * @param string $xpath
     * @param string $attribute
     */
    public function defineParameters($xpath, $attribute)
    {
        $params = $this->getAttribute($xpath . '/@' . $attribute);
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
        $xpath = $this->_getControlXpath('tab', 'general_web');
        $this->addParameter('tabName', 'web');
        $this->clickAndWait($xpath, $this->_browserTimeoutPeriod);
        $secureBaseUrlXpath = $this->_getControlXpath('field', 'secure_base_url');
        $url = preg_replace('/http(s)?/', 'https', $this->getValue($secureBaseUrlXpath));
        $data = array('secure_base_url' => $url,
            'use_secure_urls_in_' . $path => ucwords(strtolower($useSecure)));
        $this->fillForm($data, 'general_web');
        $this->clickButton('save_config');
        if ($this->getTitle() == 'Log into Magento Admin Page') {
            $this->loginAdminUser();
            $this->admin('system_configuration');
            $this->clickAndWait($xpath, $this->_browserTimeoutPeriod);
        }
        $this->assertTrue($this->verifyForm($data, 'general_web'), $this->getParsedMessages());
    }

    /**
     * @param $parameters
     */
    public function configurePaypal($parameters)
    {
        $this->configure($parameters);
    }

}