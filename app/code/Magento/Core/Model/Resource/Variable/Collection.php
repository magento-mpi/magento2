<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Custom variabel collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Variable;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Store Id
     *
     * @var int
     */
    protected $_storeId    = 0;

    /**
     *  Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('\Magento\Core\Model\Variable', '\Magento\Core\Model\Resource\Variable');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return \Magento\Core\Model\Resource\Variable\Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Add store values to result
     *
     * @return \Magento\Core\Model\Resource\Variable\Collection
     */
    public function addValuesToResult()
    {
        $this->getSelect()
            ->join(
                array('value_table' => $this->getTable('core_variable_value')),
                'value_table.variable_id = main_table.variable_id',
                array('value_table.value'));
        $this->addFieldToFilter('value_table.store_id', array('eq' => $this->getStoreId()));
        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('code', 'name');
    }
}
