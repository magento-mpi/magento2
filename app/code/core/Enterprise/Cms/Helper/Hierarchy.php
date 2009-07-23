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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * CMS Hierarchy data helper
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Helper_Hierarchy extends Mage_Core_Helper_Abstract
{
    const XML_PATH_HIERARCHY_ENABLED    = 'enterprise_cms/hierarchy/enabled';
    const XML_PATH_METADATA_ENABLED     = 'enterprise_cms/hierarchy/metadata_enabled';

    /**
     * Check is Enabled Hierarchy Functionality
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_HIERARCHY_ENABLED);
    }

    /**
     * Check is Enabled Hierarchy Metadata
     *
     * @return bool
     */
    public function isMetedataEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_METADATA_ENABLED);
    }
}
