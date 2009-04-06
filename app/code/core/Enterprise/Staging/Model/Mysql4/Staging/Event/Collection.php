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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Mysql4_Staging_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/staging_event');
    }

    /**
     * Set staging filter into collection
     *
     * @param   mixed   $stagingId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
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
     * Add backuped filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addBackupedFilter()
    {
        $this->addFieldToFilter('main_table.is_backuped', 1);

        return $this;
    }

    /**
     * Add holded status filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addHoldedFilter()
    {
        $this->addFieldToFilter('main_table.status', 'holded');

        return $this;
    }
        
    /**
     * Add merged staging filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addMergedFilter()
    {
        $this->addFieldToFilter('main_table.code', Enterprise_Staging_Model_Staging_Config::EVENT_MERGE);
        $this->addFieldToFilter('main_table.state', Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE);
        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

        return $this;
    }

    /**
     * Add merged staging filter into collection
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addRollbackFilter()
    {
        $this->addFieldToFilter('main_table.code', Enterprise_Staging_Model_Staging_Config::EVENT_ROLLBACK);
//        $this->addFieldToFilter('main_table.state', Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE);
//        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

        return $this;
    }

    /**
     * Add rollback processing filter
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addRollbackProcessingFilter()
    {
        $this->addFieldToFilter('main_table.code', Enterprise_Staging_Model_Staging_Config::EVENT_ROLLBACK);        
        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING);

        return $this;
    }

    /**
     * Add merge processing filter
     *
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function addMergeProcessingFilter()
    {
        $this->addFieldToFilter('main_table.code', Enterprise_Staging_Model_Staging_Config::EVENT_MERGE);        
        $this->addFieldToFilter('main_table.status', Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING);

        return $this;
    }
    
    
    public function addStagingToCollection()
    {
        $this->getSelect()
            ->joinLeft(
                array('staging' => $this->getTable('enterprise_staging/staging')),
                'main_table.staging_id=staging.staging_id',
                array('staging_name'=>'name')
        );

        return $this;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('event_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('event_id', 'name');
    }
}