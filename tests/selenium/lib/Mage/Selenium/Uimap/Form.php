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
 * Form uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Form extends Mage_Selenium_Uimap_Abstract
{

    /**
     * Get all fieldsets defined on the current form, as well as on all tabs of this form
     *
     * @return array
     */
    public function getFieldsets()
    {
        $fieldsets = array();
        // @TODO
        return $fieldsets;
    }

    /**
     * Get tabs defined on the current form
     *
     * @return array
     */
    public function getTabs()
    {
        $tabs = array();
        // @TODO
        return $tabs;
    }

    /**
     * Get buttons defined on the current form
     *
     * @return array
     */
    public function getButtons()
    {
        $buttons = array();
        // @TODO
        return $buttons;
    }

    /**
     * Get all fields defined on the current form, as well as on its tabs and fieldsets
     *
     * @return array
     */
    public function getAllFields()
    {
        $fields = array();
        // @TODO
        return $fields;
    }

}
