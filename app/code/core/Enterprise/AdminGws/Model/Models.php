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
 * Models limiter
 *
 */
class Enterprise_AdminGws_Model_Models
{
    /**
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize helper
     *
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('enterprise_admingws');
    }

    /**
     * Limit CMS page save
     *
     * @param Mage_Cms_Model_Page $model
     */
    public function cmsPageSaveBefore($model)
    {
        $model->setData('stores', $this->_updateSavingStoreIds(
            $model->getData('stores'), $model->getResource()->lookupStoreIds($model->getId()))
        );
    }

    /**
     * Limit CMS block save
     *
     * @param Mage_Cms_Model_Block $model
     */
    public function cmsBlockSaveBefore($model)
    {
        $model->setData('stores', $this->_updateSavingStoreIds(
            $model->getData('stores'), $model->getResource()->lookupStoreIds($model->getId()))
        );
    }

    /**
     * Limit CMS Poll save
     *
     * @param Mage_Poll_Model_Poll $model
     */
    public function pollSaveBefore($model)
    {
        $model->setData('store_ids', $this->_updateSavingStoreIds(
            $model->getData('store_ids'), $model->getResource()->lookupStoreIds($model->getId()))
        );
    }

    /**
     * Limit Rule save
     *
     * @param Mage_Rule_Model_Rule $model
     * @return void
     */
    public function ruleSaveBefore($model)
    {
        $originalWebsiteIds = explode(',', $model->getOrigData('website_ids'));
        $websiteIds = explode(',', $model->getData('website_ids'));

        $updatedWebsiteIds = $this->_updateSavingWebsiteIds(
           $websiteIds, $originalWebsiteIds
        );

        $model->setData('website_ids', implode(',', $updatedWebsiteIds));
    }

    /**
     * Limit newsletter queue save
     *
     * @param Mage_Newsletter_Model_Queque $model
     * @return void
     */
    public function newsletterQueueSaveBefore($model)
    {
        if ($model->getSaveStoresFlag()) {
            $model->setStores($this->_updateSavingStoreIds(
                $model->getStores(),
                $model->getResource()->getStores($model)
            ));
        }
    }

    /**
     * Limit incoming store IDs to allowed and add disallowed original stores
     *
     * @param array $newIds
     * @param array $origIds
     * @return array
     */
    protected function _updateSavingStoreIds($newIds, $origIds)
    {
        return array_unique(array_merge(
            array_intersect($newIds, $this->_helper->getStoreIds()),
            array_intersect($origIds, $this->_helper->getDisallowedStoreIds())
        ));
    }


    /**
     * Limit incoming website IDs to allowed and add disallowed original websites
     *
     * @param array $newIds
     * @param array $origIds
     * @return array
     */
    protected function _updateSavingWebsiteIds($newIds, $origIds)
    {
        return array_unique(array_merge(
            array_intersect($newIds, $this->_helper->getWebsiteIds()),
            array_intersect($origIds, $this->_helper->getDisallowedWebsiteIds())
        ));
    }
}
