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
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Helper for API2 resource item and collection models
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Validator
{
    /**
     * Check if passed stores are valid
     *
     * @param array $stores
     * @return bool
     */
    public function areStoresValid($stores)
    {
        if (!is_array($stores)) {
            return false;
        }
        $validStores = array();
        foreach (Mage::app()->getStores(true) as $store) {
            $validStores[] = $store->getId();
        }
        foreach ($stores as $store) {
            if (!in_array($store, $validStores)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if passed review status id is valid
     *
     * @param $statusId
     * @return bool
     */
    public function isStatusValid($statusId)
    {
        $validStatusList = array();
        $statusList = Mage::getModel('review/review')->getStatusCollection()->load()->toArray();
        foreach ($statusList['items'] as $status) {
            $validStatusList[] = $status['status_id'];
        }
        if (!in_array($statusId, $validStatusList)) {
            return false;
        }

        return true;
    }
}
