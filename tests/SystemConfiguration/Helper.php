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
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SystemConfiguration_Helper extends Mage_Selenium_TestCase
{
    /**
     * System Configuration
     *
     * @param array|string $parameters
     */
    public function configure($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $parameters = $this->arrayEmptyClear($parameters);
        $this->navigate('system_configuration');
        $chooseScope = (isset($parameters['configuration_scope'])) ? $parameters['configuration_scope'] : NULL;
        if ($chooseScope) {
            $xpath = $this->_getControlXpath('dropdown', 'current_configuration_scope');
            $toSelect = $xpath . '//option[normalize-space(text())="' .
                    $parameters['configuration_scope'] . '"]';
            $isSelected = $toSelect . '[@selected]';
            $this->defineParameters($toSelect, '@url');
            if (!$this->isElementPresent($isSelected)) {
                $this->fillForm(array('current_configuration_scope' => $parameters['configuration_scope']));
                $this->waitForPageToLoad();
            }
        }
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $tab = (isset($value['tab_name'])) ? $value['tab_name'] : NULL;
                $settings = (isset($value['configuration'])) ? $value['configuration'] : NULL;
                if ($tab) {
                    $xpath = $this->_getControlXpath('tab', $tab);
                    $this->clickAndWait($xpath);
                    $this->fillForm($settings, $tab);
                    $this->defineParameters($xpath, '@href');
                    $this->saveForm('save_config');
                }
            }
        }
    }

    private function defineParameters($xpath, $attribute)
    {
        $params = $this->getAttribute($xpath . $attribute);
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
}

