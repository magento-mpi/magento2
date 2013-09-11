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
 * Tax report resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Report;

class Tax extends \Magento\Reports\Model\Resource\Report\AbstractReport
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Tax\Model\Resource\Report\Tax
     */
    public function aggregate($from = null, $to = null)
    {
        \Mage::getResourceModel('Magento\Tax\Model\Resource\Report\Tax\Createdat')->aggregate($from, $to);
        \Mage::getResourceModel('Magento\Tax\Model\Resource\Report\Tax\Updatedat')->aggregate($from, $to);
        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_TAX_FLAG_CODE);

        return $this;
    }
}
