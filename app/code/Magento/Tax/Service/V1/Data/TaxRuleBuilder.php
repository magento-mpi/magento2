<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

/**
 * Builder for the TaxRule Service Data Object
 *
 *  * @method TaxRule create()
 */
class TaxRuleBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set id
     *
     * @param int
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(TaxRule::ID, $id);
    }

    /**
     * Set code
     *
     * @param String
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(TaxRule::CODE, $code);
    }

    /**
     * Set customer tax class id
     *
     * @param int
     * @return $this
     */
    public function setCustomerTaxClassId($customerTaxClassId)
    {
        return $this->_set(TaxRule::CUSTOMER_TAX_CLASS_ID, $customerTaxClassId);
    }

    /**
     * Set product tax class id
     *
     * @param int
     * @return $this
     */
    public function setProductTaxClassId($productTaxClassId)
    {
        return $this->_set(TaxRule::PRODUCT_TAX_CLASS_ID, $productTaxClassId);
    }

    /**
     * Set tax rates
     *
     * @param Magento\Tax\Service\V1\Data\TaxRate[]| null $taxRates
     * @return $this
     */
    public function setTaxRates($taxRates)
    {
        return $this->_set(TaxRule::TAX_RATES, $taxRates);
    }

    /**
     * Set priority
     *
     * @param int
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->_set(TaxRule::PRIORITY, $priority);
    }

    /**
     * Set sort order.
     *
     * @param int
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->_set(TaxRule::SORT_ORDER, $sortOrder);
    }
}