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
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog Event data helper
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EVENT_LISTER_OUTPUT = 'catalog/enterprise_catalogevent/lister_output';
    const XML_PATH_ENABLED = 'catalog/enterprise_catalogevent/enabled';
    const XML_PATH_EVENT_LISTER_ITEMS_NUMBER_CATEGORY = 'catalog/enterprise_catalogevent/lister_items_number_category';
    const XML_PATH_EVENT_LISTER_ITEMS_NUMBER_CMS = 'catalog/enterprise_catalogevent/lister_items_number_cms';

    /**
     * Retreive event image url
     *
     * @param Enterprise_CatalogEvent_Model_Event
     * @return string|boolean
     */
    public function getEventImageUrl($event)
    {
        if ($event->getImage()) {
            return $event->getImageUrl();
        }

        return false;
    }

    /**
     * Retreive configuration value for event lister block output
     *
     * @return boolean
     */
    public function isEnabledEventLister()
    {
        return $this->isEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_EVENT_LISTER_OUTPUT);
    }

    /**
     * Retrieve configuration value for enabled of catalog event
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Retreive items number for lister
     *
     * @return int
     */
    public function getListerItemsNumber()
    {
        if (Mage::registry('current_category')) {
            return Mage::getStoreConfig(self::XML_PATH_EVENT_LISTER_ITEMS_NUMBER_CATEGORY);
        }

        return Mage::getStoreConfig(self::XML_PATH_EVENT_LISTER_ITEMS_NUMBER_CMS);
    }
}
