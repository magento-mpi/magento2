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
class Enterprise2_Mage_CmsWidgets_Helper extends Core_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            $xpath = $this->_getControlXpath('dropdown', 'type');
            $type = $this->getValue($xpath . '/option[text()="' . $settings['type'] . '"]');
            $this->addParameter('type', str_replace('/', '-', $type));
            $packageTheme = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->addParameter('package', $packageTheme[0]);
            $this->addParameter('theme', $packageTheme[1]);
            if (($packageTheme[0] ==  $packageTheme[1]) && ($packageTheme[1] == 'Enterprise'))
                $this->addParameter('package', 'default');
            $this->fillFieldset(array('type' => $settings['type']), 'settings_fieldset');
            $this->selectDesignPackageTheme('design_package_theme', $packageTheme[0],$packageTheme[1]);
        }
        $this->clickButton('continue', false);
        $this->pleaseWait();
        $this->validatePage('add_widget_options');
    }
        
    /**
     * Selects a store view from 'Choose Store View' drop-down in backend
     *
     * @param string $controlName Name of the dropdown from UIMaps
     * @param string $package Default = 'Enterprise'
     * @param string $theme Default = 'default'
     *
     * @throws PHPUnit_Framework_Exception
     */
    public function selectDesignPackageTheme($controlName, $package = 'Enterprise', $theme = 'default')
    {
        $fieldXpath = $this->_getControlXpath('dropdown', $controlName);
        $themeXpath = $fieldXpath
                          . "/optgroup[normalize-space(@label) = '$package']"
                          . "/option[contains(text(),'$theme')]";
        if(!$this->isElementPresent($themeXpath)) {
            throw new PHPUnit_Framework_Exception('Cannot find option ' . $themeXpath);
        }
        $optionValue = $this->getValue($themeXpath);
        //Try to select by value first, since there may be options with equal labels.
        if (isset($optionValue)) {
            $this->select($fieldXpath, 'value=' . $optionValue);
        } else {
            $this->select($fieldXpath, 'label=' . 'regexp:^\s+' . preg_quote($theme));
        }
    }
}