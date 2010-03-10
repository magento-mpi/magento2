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
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_SalesPool_Model_Pool_Check
{
    /**
     * events areas
     * @var $_eventAreas array
     */
    protected $_eventAreas = array(
        Mage_Core_Model_App_Area::AREA_ADMIN,
        Mage_Core_Model_App_Area::AREA_GLOBAL,
        Mage_Core_Model_App_Area::AREA_FRONTEND
    );

    /**
     * List of related events
     * @var array
     */
    protected $_events = array(
        'sales_order_save_after',
        'sales_order_address_save_after',
        'sales_order_payment_save_after',
        'sales_order_item_save_after',
        'sales_order_status_history_save_after',
        'sales_payment_transaction_save_after',
    );

    /**
     * list of related models
     * @var $_models array
     */
    protected $_models = array(
        'sales/order'           => 'Mage_Sales_Model_Order',
        'sales/order_payment'   => 'Mage_Sales_Model_Order_Payment',
        'sales/order_address'   => 'Mage_Sales_Model_Order_Address',
        'sales/order_item'      => 'Mage_Sales_Model_Order_Item',
    );

    /**
     * Get list of potential problems
     * @return array
     */
    public function getProblems()
    {
        $problems = array();
        $referredTables = Mage::getResourceSingleton('enterprise_salespool/pool')->getReferredTables();
        $modulesObservers = $this->_getModulesObservers();
        foreach ($modulesObservers as $code => $observers) {
            foreach ($referredTables as $baseTable => $referredTable) {
                if (strpos($referredTable, $code) !== false) {
                    $problems[] = Mage::helper('enterprise_salespool')->__('Foreign Key from "%s" to "%s" table', $referredTable, $baseTable);
                }
            }
        }
        foreach ($this->_models as $model => $class) {
            $modelClass = Mage::getModel($model);
            $modelClass = get_class($modelClass);
            if ($class != $modelClass) {
                $problems[] = Mage::helper('enterprise_salespool')->__('"%s" moder overwriting', $model);
            }
        }
        return $problems;
    }

    /**
     * Get list of observers per module
     * @return array
     */
    protected function _getModulesObservers()
    {
        $observers = array();
        foreach ($this->_eventAreas as $area) {
            foreach ($this->_events as $event) {
                $config = Mage::getConfig()->getEventConfig($area, $event);
                if ($config) {
                    $list = $config->asArray();
                    $observers = array_merge($observers, $list['observers']);
                }
            }
        }
        $res = array();
        foreach ($observers as $data) {
            if (isset($data['class'])) {
                $info = explode('/', $data['class']);
                if (!isset($res[$info[0]])) {
                    $res[$info[0]] = array();
                }
                $res[$info[0]][] = $data;
            }
        }
        return $res;
    }
}