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
 * Store group model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Core_Model_Store_Group extends Mage_Core_Model_Abstract
{
    protected $_stores = array();
    protected function _construct()
    {
        $this->_init('core/store_group');
    }

    public function addStore(Mage_Core_Model_Store $model)
    {
        $this->_stores[spl_object_hash($model)] = $model;
        return $this;
    }

    public function getStores()
    {
        return $this->_stores;
    }

    public function isCanDelete()
    {
        $size = $this->getCollection()->addWebsiteFilter($this->getWebsiteId())->getSize();
        $delete = false;
        $stores = Mage::getModel('core/store')->getCollection()->addGroupFilter();
        foreach ($stores as $store) {
            if ($store->getCode() == 'base') {
                $delete = true;
            }
        }
        return ($size > 1 && !$delete);
    }
}