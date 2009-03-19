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

/**
 * Staging store model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Store_Group extends Mage_Core_Model_Abstract
{
	const EXCEPTION_LOGIN_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_LOGIN_OR_PASSWORD = 2;

    protected $_items;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_store_group');
    }

    public function getMasterStore()
    {
    	$masterStoreId = $this->getMasterStoreId();
    	if (!is_null($masterStoreId)) {
    		return Mage::app()->getStore($masterStoreId);
    	} else {
    		return false;
    	}
    }

    public function getSlaveStore()
    {
        $slaveStoreId = $this->getSlaveStoreId();
        if (!is_null($slaveStoreId)) {
            return Mage::app()->getStore($slaveStoreId);
        } else {
            return false;
        }
    }

    public function loadBySlaveStoreGroupId($id)
    {
        $this->getResource()->loadBySlaveStoreGroupId($this, $id);

        return $this;
    }

    public function syncWithStoreGroup(Mage_Core_Model_Store_Group $group)
    {
        $this->getResource()->syncWithStoreGroup($this, $group);

        return $this;
    }
}