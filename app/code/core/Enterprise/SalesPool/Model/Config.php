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

/**
 * Pool config model
 *
 */
class Enterprise_SalesPool_Model_Config
{
    const XML_PATH_POOL_ACTIVE = 'sales/enterprise_salespool/active';
    const XML_PATH_POOL_ACTIVE_ADMIN = 'sales/enterprise_salespool/active_admin';
    const XML_PATH_POOL_FLUSH_PERIOD = 'sales/enterprise_salespool/flush_period';
    const XML_PATH_POOL_ALLOWED_PAYMENT_METHODS = 'sales/enterprise_salespool/allowed_payment_methods';
    const XML_PATH_POOL_ALLOWED_PAYMENT_METHODS_SCOPE = 'sales/enterprise_salespool/allowed_payment_methods_scope';
    const XML_PATH_POOL_ENTITIES = 'global/enterprise/salespool/entities';

    protected $_entities = array(
        'order' => array(
            'source' => 'sales/order',
            'fields' => array(
                'create_invoice' => 'boolean',
                'serialized_invoice_data' => 'text',
                'in_pool'=>'boolean',
                'shipping_name' => 'varchar',
                'billing_name' => 'varchar'
            ),
            'ignore' => array(
                'base_discount_refunded',
                'base_shipping_refunded',
                'base_shipping_tax_refunded',
                'base_subtotal_refunded',
                'base_tax_refunded',
                'base_total_offline_refunded',
                'base_total_online_refunded',
                'base_total_refunded',
                'discount_refunded',
                'shipping_refunded',
                'shipping_tax_refunded',
                'subtotal_refunded',
                'tax_refunded',
                'total_offline_refunded',
                'total_online_refunded',
                'total_refunded',
            )
        ),
        'order_item' => array(
            'source' => 'sales/order_item',
            'primary' => 'item_id',
            'ignore' => array(
                'qty_refunded',
                'qty_shipped',
                'amount_refunded',
                'base_amount_refunded',
            )
        ),
        'order_payment' => array(
            'source' => 'sales/order_payment'
        ),
        'order_payment_transaction' => array(
            'source' => 'sales/payment_transaction',
            'primary' => 'transaction_id'
        ),
        'order_address' => array(
            'source' => 'sales/order_address'
        ),
        'order_status_history' => array(
            'source' => 'sales/order_status_history'
        )
    );

    /**
     * @var $_isActive bool
     */
    protected $_isActive = false;

    protected $_entitiesInitialized = false;

    public function __construct()
    {
        $this->_isActive = Mage::getStoreConfigFlag(self::XML_PATH_POOL_ACTIVE);
    }

    /**
     * Check is pool active
     *
     * @return boolean
     */
    public function isPoolActive()
    {
        return $this->_isActive;
    }

    /**
     * Check is pool active in admin
     *
     * @return boolean
     */
    public function isPoolActiveAdmin()
    {
        return $this->isPoolActive() && Mage::getStoreConfigFlag(self::XML_PATH_POOL_ACTIVE_ADMIN);
    }

    /**
     * Retrieve not allowed payment methods for pool
     *
     * @return boolean
     */
    public function getAllowedPaymentMethods()
    {
        $data = Mage::getStoreConfig(self::XML_PATH_POOL_ALLOWED_PAYMENT_METHODS);
        if (empty($data)) {
            return array();
        }

        return explode(',', $data);
    }

    /**
     * Retrieve not allowed payment methods for pool
     *
     * @return boolean
     */
    public function getUseAllowedPaymentMethods()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_POOL_ALLOWED_PAYMENT_METHODS_SCOPE);
    }

    /**
     * Check pool activity in current store for current object
     *
     * @return boolean
     */
    public function isCurrentlyPoolActive($object)
    {
        if (!$object->getId() && $this->getUseAllowedPaymentMethods()) {
            if ($object instanceof Mage_Sales_Model_Order) {
                if (!in_array($object->getPayment()->getMethod(), $this->getAllowedPaymentMethods())) {
                    return false;
                }
            } elseif ($object->getOrder()) {
                if (in_array($object->getOrder()->getPayment()->getMethod(), $this->getAllowedPaymentMethods())) {
                    return false;
                }
            }
        }

        if (Mage::app()->getStore()->isAdmin()) {
            return $this->isPoolActiveAdmin();
        }

        return $this->isPoolActive();
    }



    /**
     * Retrieve pool period in minutes
     *
     * @return int
     */
    public function getPoolFlushPeriod()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_POOL_FLUSH_PERIOD);
    }

    /**
     * Initialize pool entities
     *
     * @return Enterprise_SalesPool_Model_Config
     */
    protected function _initPoolEntities()
    {
        if ($this->_entitiesInitialized === true) {
            return $this;
        }

        $node = Mage::getConfig()->getNode(self::XML_PATH_POOL_ENTITIES);
        foreach ($this->_entities as $entityCode => &$entityConfig) {
            if (isset($node->{$entityCode})) {
                if (isset($node->{$entityCode}->fields)) {
                    foreach ($node->{$entityCode}->fields->children() as $field => $type) {
                        $type = (string) $type;
                        $entityConfig['fields'][$field] = $type;
                    }
                }

                if (isset($node->{$entityCode}->ignore)) {
                    foreach ($node->{$entityCode}->ignore->children() as $field => $dummy) {
                        $entityConfig['ignore'][] = $field;
                    }
                }
            }
        }

        $this->_entitiesInitialized = true;
        return $this;
    }

    /**
     * Retrieve pool entities
     *
     * @return array
     */
    public function getPoolEntities()
    {
        $this->_initPoolEntities();
        return $this->_entities;
    }

    /**
     * Retrieve pool entity
     *
     * @param string $entity
     * @return array|false
     */
    public function getPoolEntity($entity)
    {
        $this->_initPoolEntities();
        if (isset($this->_entities[$entity])) {
            return $this->_entities[$entity];
        }

        return false;
    }
}
