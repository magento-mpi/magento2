<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Configuration source for customer group multiselect
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_Cms_Model_Adminhtml_System_Config_Source_Wysiwyg_Enabled
{
    const ENABLED_DEFAULT = 1;
    const DISABLED_DEFAULT = 2;
    const DISABLED_TOTALLY = 3;

    public function toOptionArray()
    {
        return array(
            array('value' => self::ENABLED_DEFAULT, 'label' => Mage::helper('enterprise_cms')->__('Enable by Default')),
            array('value' => self::DISABLED_DEFAULT, 'label' => Mage::helper('enterprise_cms')->__('Disabled by Default')),
            array('value' => self::DISABLED_TOTALLY, 'label' => Mage::helper('enterprise_cms')->__('Disable Completely'))
        );
    }
}
