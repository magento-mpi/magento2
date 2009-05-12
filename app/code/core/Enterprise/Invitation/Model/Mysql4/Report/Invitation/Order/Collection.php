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
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reports invitation order report collection
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Mysql4_Report_Invitation_Order_Collection
    extends Enterprise_Invitation_Model_Mysql4_Report_Invitation_Collection
{
    /**
     * Add map property
     *
     * @var array
     */
    protected $_map = array();

    /**
     * Join custom fields
     *
     * @return Enterprise_Invitation_Model_Mysql4_Report_Invitation_Order_Collection
     */
    protected function _joinFields()
    {
        $this->getSelect()
            ->joinLeft(array('order'=>$this->getTable('sales/order')),
                       'order.customer_id = main_table.referral_id',
                       array(
                            'purchased' => new Zend_Db_Expr('COUNT(DISTINCT order.customer_id)'),
                            'purchased_rate' =>  new Zend_Db_Expr('IF(COUNT(DISTINCT main_table.referral_id), COUNT(DISTINCT order.customer_id) / COUNT(DISTINCT main_table.referral_id) > 0, 0) * 100'),
                       ));

        $this->_map['fields']['order_store_id'] = 'order.store_id';
        return $this;
    }

    /**
     * Filters report by stores
     *
     * @param array $storeIds
     * @return Enterprise_Invitation_Model_Mysql4_Report_Invitation_Order_Collection
     */
    public function setStoreIds($storeIds)
    {
        parent::setStoreIds($storeIds);
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->addFieldToFilter('order_store_id', array('in' => (array)$storeIds));
        }

        return $this;
    }
}
