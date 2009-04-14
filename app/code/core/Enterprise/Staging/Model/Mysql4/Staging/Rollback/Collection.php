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

class Enterprise_Staging_Model_Mysql4_Staging_Rollback_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/staging_rollback');
    }

    /**
     * Set staging filter into collection
     *
     * @param   mixed   $stagingId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Rollback_Collection
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
     * Set backup filter into collection
     *
     * @param   mixed   $backupId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Rollback_Collection
     */
    public function setBackupFilter($backupId)
    {
        if (is_object($backupId)) {
            $backupId = $backupId->getId();
        }
        $this->addFieldToFilter('backup_id', (int) $backupId);

        return $this;
    }

    /**
     * Add complete filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Rollback_Collection
     */
    public function addCompleteFilter()
    {
        $this->addFieldToFilter('main_table.state', Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE);
        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

        return $this;
    }
    
    public function addEventToCollection()
    {
        $this->getSelect()
            ->joinLeft(
                array('enterprise_staging_event' => $this->getTable('enterprise_staging/staging_event')),
                'main_table.event_id=enterprise_staging_event.event_id',
                array(
                    'event_comment' =>  'comment',
                    'event_user_id' =>  'user_id',
                    'event_id'      =>  'event_id',
                    'event_ip'      =>  'ip'
                )
        );

        return $this;
    }    
    public function toOptionArray()
    {
        return parent::_toOptionArray('rollback_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('rollback_id', 'name');
    }
}