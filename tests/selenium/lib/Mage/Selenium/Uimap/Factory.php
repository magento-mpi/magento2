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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Factory
{
    /**
     *
     * @param <type> $elemKey
     * @param <type> $elemValue
     * @return <type> 
     */
    public static function createUimapElement($elemKey, &$elemValue) {
        $elements = null;
        switch($elemKey)
        {
            case 'form':
                $elements = new Mage_Selenium_Uimap_Form($elemValue);
                break;
            case 'tabs':
                $elements = new Mage_Selenium_Uimap_TabsCollection();
                foreach($elemValue as $tabArrayKey=>&$tabArrayValue) {
                    foreach($tabArrayValue as $tabKey=>&$tabValue) {
                        $elements[$tabKey] = new Mage_Selenium_Uimap_Tab($tabKey, $tabValue);
                    }
                }
                break;
            case 'fieldsets':
                $elements = new Mage_Selenium_Uimap_FieldsetsCollection();
                foreach($elemValue as $fieldsetArrayKey=>&$fieldsetArrayValue) {
                    foreach($fieldsetArrayValue as $fieldsetKey=>&$fieldsetValue) {
                        $elements[$fieldsetKey] = new Mage_Selenium_Uimap_Fieldset($fieldsetKey, $fieldsetValue);
                    }
                }
                break;
            case 'buttons':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('buttons', $elemValue);
                break;
            case 'messages':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('messages', $elemValue);
                break;
            case 'links':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('links', $elemValue);
                break;
            case 'fields':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('fields', $elemValue);
                break;
            case 'dropdowns':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('dropdowns', $elemValue);
                break;
            case 'checkboxes':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('checkboxes', $elemValue);
                break;
            case 'radiobuttons':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('radiobuttons', $elemValue);
                break;
            case 'required':
                $elements = new Mage_Selenium_Uimap_ElementsCollection('required', $elemValue);
                break;
        }

        return $elements;
    }

}
