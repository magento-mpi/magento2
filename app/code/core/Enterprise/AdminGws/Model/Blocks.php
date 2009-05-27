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
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Blocks limiter
 *
 */
class Enterprise_AdminGws_Model_Blocks extends Enterprise_AdminGws_Model_Observer_Abstract
{
    /**
     * Check whether category can be moved
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryIsMoveable($observer)
    {
        if ($this->_helper->getIsAll()) { // because observer is passed through directly
            return;
        }
        $category = $observer->getEvent()->getOptions()->getCategory();
        if (!$this->_helper->hasExclusiveCategoryAccess($category->getData('path'))) {
            $observer->getEvent()->getOptions()->setIsMoveable(false);
        }
    }

    /**
     * Check whether category can be added
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryCanBeAdded($observer)
    {
        if ($this->_helper->getIsAll()) { // because observer is passed through directly
            return;
        }
        $observer->getEvent()->getOptions()->setIsAllow(false);
    }

    /**
     * Restrict customer grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetCustomerGridContainer($observer)
    {
        if (!$this->_helper->getWebsiteIds()) {
            $observer->getBlock()->removeButton('add');
        }
    }

    /**
     * Restrict system stores page container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetManageStores($observer)
    {
        $block = $observer->getBlock();
        $block->removeButton('add');
        if (!$this->_helper->getWebsiteIds()) {
            $block->removeButton('add_group');
            $block->removeButton('add_store');
        }
    }

    /**
     * Restrict product grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetProductGridContainer($observer)
    {
        if (!$this->_helper->getWebsiteIds()) {
            $observer->getBlock()->removeButton('add_new');
        }
    }

    /**
     * Restrict event grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetCatalogEventGridContainer($observer)
    {
        if (!$this->_helper->getWebsiteIds()) {
            $observer->getBlock()->removeButton('add');
        }
    }
}
