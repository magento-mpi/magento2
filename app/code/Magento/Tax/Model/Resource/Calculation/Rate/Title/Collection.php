<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Title Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Calculation\Rate\Title;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Calculation\Rate\Title', 'Magento\Tax\Model\Resource\Calculation\Rate\Title');
    }

    /**
     * Add rate id filter
     *
     * @param int $rateId
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Title\Collection
     */
    public function loadByRateId($rateId)
    {
        $this->addFieldToFilter('main_table.tax_calculation_rate_id', $rateId);
        return $this->load();
    }
}
