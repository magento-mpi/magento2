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
 * Tax Rate Title Model
 *
 * @method \Magento\Tax\Model\Resource\Calculation\Rate\Title _getResource()
 * @method \Magento\Tax\Model\Resource\Calculation\Rate\Title getResource()
 * @method int getTaxCalculationRateId()
 * @method \Magento\Tax\Model\Calculation\Rate\Title setTaxCalculationRateId(int $value)
 * @method int getStoreId()
 * @method \Magento\Tax\Model\Calculation\Rate\Title setStoreId(int $value)
 * @method string getValue()
 * @method \Magento\Tax\Model\Calculation\Rate\Title setValue(string $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Calculation\Rate;

class Title extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\Tax\Model\Resource\Calculation\Rate\Title');
    }

    public function deleteByRateId($rateId)
    {
        $this->getResource()->deleteByRateId($rateId);
        return $this;
    }
}
