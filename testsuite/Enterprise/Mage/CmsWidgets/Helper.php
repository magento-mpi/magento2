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
class Enterprise_Mage_CmsWidgets_Helper extends Core_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            $this->addParameter('dropdownXpath', $this->_getControlXpath('dropdown', 'type'));
            $this->addParameter('optionText', $settings['type']);
            $type = $this->getControlAttribute('pageelement', 'dropdown_option_text', 'value');
            $this->addParameter('type', str_replace('/', '-', $type));
            $packageTheme = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->addParameter('package', $packageTheme[0]);
            $this->addParameter('theme', $packageTheme[1]);
            $this->fillFieldset($settings, 'settings_fieldset');
        }
        $this->clickButton('continue', false);
        $this->pleaseWait();
        $this->validatePage('add_widget_options');
    }
}