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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Mysql4_Staging_Backup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/staging_backup');
    }

    /**
     * Set staging filter into collection
     *
     * @param   mixed   $stagingId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Backup_Collection
     */
    public function setStagingFilter($stagingId)
    {
        if (is_object($stagingId)) {
            $stagingId = $stagingId->getId();
        }
        $this->addFieldToFilter('staging_id', (int) $stagingId);

        return $this;
    }

    /**
     * Set event filter into collection
     *
     * @param   mixed   $eventId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Backup_Collection
     */
    public function setEventFilter($eventId)
    {
        if (is_object($eventId)) {
            $eventId = $eventId->getId();
        }
        $this->addFieldToFilter('event_id', (int) $eventId);

        return $this;
    }

    /**
     * Add backuped filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Backup_Collection
     */
    public function addBackupedFilter()
    {
        $this->addFieldToFilter('main_table.is_backuped', 1);

        return $this;
    }

    /**
     * Add merged staging filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Backup_Collection
     */
    public function addMergedFilter()
    {
        $this->addFieldToFilter('main_table.code', Enterprise_Staging_Model_Staging_Config::EVENT_MERGE);
        $this->addFieldToFilter('main_table.state', Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE);
        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

        return $this;
    }

    public function addStagingToCollection($addWebsiteData = false)
    {
        $this->getSelect()
            ->joinLeft(
                array('staging' => $this->getTable('enterprise_staging/staging')),
                'main_table.staging_id=staging.staging_id',
                array('staging_name'=>'name')
        );

        if ($addWebsiteData) {
            $this->getSelect()
                ->joinLeft(
                    array('core_website' => $this->getTable('core/website')),
                    'staging.master_website_id=core_website.website_id',
                    array('master_website_id' => 'website_id',
                        'master_website_name' => 'name'))
                ->joinLeft(
                    array('staging_website' => $this->getTable('core/website')),
                    'staging.staging_website_id=staging_website.website_id',
                    array('staging_website_id' => 'website_id',
                        'staging_website_name' => 'name')
            );

        }

        return $this;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('backup_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('backup_id', 'name');
    }
}