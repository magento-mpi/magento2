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
class Community2_Mage_CmsWidgets_Helper extends Core_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            list($package, $theme) = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->fillDropdown('type', $settings['type']);
            $type = $this->getControlAttribute('dropdown', 'type', 'selectedValue');
            $this->addParameter('type', $type);

            $xpath = $this->_getControlXpath('dropdown', 'design_package_theme');
            $xpathValue = $xpath . "/optgroup[@label='" . ucfirst(strtolower($package)) . "']/option[text()='"
                          . ucfirst(strtolower($theme)) . "']";
            $value = $this->getValue($xpathValue);
            $this->addParameter('package_theme', str_replace('/', '-', $value));
            $this->fillDropdown('design_package_theme', $value);
        }
        $waitCondition = array($this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('fieldset', 'layout_updates_header',
                                   $this->getUimapPage('admin', 'add_widget_options')));
        $this->clickButton('continue', false);
        $this->waitForElement($waitCondition);
        $this->validatePage('add_widget_options');
    }
}