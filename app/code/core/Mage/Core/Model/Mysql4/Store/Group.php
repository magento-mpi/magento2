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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store group resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Core_Model_Mysql4_Store_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/store_group', 'group_id');
    }

    protected function _afterSave(Mage_Core_Model_Abstract $model)
    {
        if ($model->getStores()) {
            $defaultUpdate = false;
            $defaultId = 0;
            if (!$model->getDefaultStoreId()) {
                $defaultUpdate = true;
            }
            foreach ($model->getStores() as $store) {
                if ($store instanceof Mage_Core_Model_Store) {
                    $store->setWebsiteId($model->getWebsiteId());
                    $store->setGroupId($model->getId());
                    $store->save();
                    if ($defaultUpdate && !$defaultId) {
                        $defaultId = $store->getId();
                    }
                }
            }
            if ($defaultUpdate && $defaultId) {
                $this->_saveDefaultStore($model->getId(), $defaultId);
            }
        }
        $this->_updateStoreWebsite($model->getId(), $model->getWebsiteId());

        return $this;
    }

    protected function _updateStoreWebsite($groupId, $websiteId)
    {
        $write = $this->_getWriteAdapter();
        $bind = array('website_id'=>$websiteId);
        $condition = $write->quoteInto('group_id=?', $groupId);
        $this->_getWriteAdapter()->update($this->getTable('core/store'), $bind, $condition);
        return $this;
    }

    protected function _saveDefaultStore($groupId, $storeId)
    {
        $write = $this->_getWriteAdapter();
        $bind = array('default_store_id'=>$storeId);
        $condition = $write->quoteInto('group_id=?', $groupId);
        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $condition);
        return $this;
    }
}