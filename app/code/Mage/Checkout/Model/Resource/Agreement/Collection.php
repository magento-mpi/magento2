<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource Model for Agreement Collection
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Resource_Agreement_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_map = array('fields' => array(
        'agreement_id' => 'main_table.agreement_id',
    ));

    /**
     * Is store filter with admin store
     *
     * @var bool
     */
    protected $_isStoreFilterWithAdmin   = true;

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Checkout_Model_Agreement', 'Mage_Checkout_Model_Resource_Agreement');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param int|Magento_Core_Model_Store $store
     * @return Mage_Checkout_Model_Resource_Agreement_Collection
     */
    public function addStoreFilter($store)
    {
        // check and prepare data
        if ($store instanceof Magento_Core_Model_Store) {
            $store = array($store->getId());
        } elseif (is_numeric($store)) {
            $store = array($store);
        }

        $alias = 'store_table_' . implode('_', $store);
        if ($this->getFlag($alias)) {
            return $this;
        }

        $storeFilter = array($store);
        if ($this->_isStoreFilterWithAdmin) {
            $storeFilter[] = 0;
        }

        // add filter
        $this->getSelect()->join(
            array($alias => $this->getTable('checkout_agreement_store')),
            'main_table.agreement_id = ' . $alias . '.agreement_id',
            array()
        )
        ->where($alias . '.store_id IN (?)', $storeFilter)
        ->group('main_table.agreement_id');

        $this->setFlag($alias, true);
        return $this;
    }

    /**
     * Make store filter using admin website or not
     *
     * @param bool $value
     * @return Mage_Checkout_Model_Resource_Agreement_Collection
     */
    public function setIsStoreFilterWithAdmin($value)
    {
        $this->_isStoreFilterWithAdmin = (bool)$value;
        return $this;
    }
}
