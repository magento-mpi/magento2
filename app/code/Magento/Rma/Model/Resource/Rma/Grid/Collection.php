<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model\Resource\Rma\Grid;

/**
 * RMA grid collection
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Rma\Model\Resource\Rma\Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'rma_rma_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'rma_grid_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_rma_grid');
    }

    /**
     * Get SQL for get record count
     *
     * @return \Magento\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $unionSelect = clone $this->getSelect();

        $unionSelect->reset(\Zend_Db_Select::ORDER);
        $unionSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $unionSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $countSelect = clone $this->getSelect();
        $countSelect->reset();
        $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');

        return $countSelect;
    }

    /**
     * Emulate simple add attribute filter to collection
     *
     * @param string $attribute
     * @param mixed $condition
     * @return \Magento\Rma\Model\Resource\Rma\Grid\Collection
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        if (!is_string($attribute) || $condition === null) {
            return $this;
        }

        return $this->addFieldToFilter($attribute, $condition);
    }
}
