<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\ExtensibleObjectBuilder;
use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\ObjectFactory;

/**
 * Builder for the TaxRule Service Data Object
 *
 * @method \Magento\Tax\Service\V1\Data\TaxRule create()
 */
class TaxRuleBuilder extends ExtensibleObjectBuilder
{
    /**
     * TaxRate builder
     *
     * @var TaxRateBuilder
     */
    protected $taxRateBuilder;

    /**
     * Initialize dependencies.
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param TaxRateBuilder $taxRateBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        TaxRateBuilder $taxRateBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->taxRateBuilder = $taxRateBuilder;
    }
    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(TaxRule::ID, $id);
    }

    /**
     * Set code
     *
     * @param String $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(TaxRule::CODE, $code);
    }

    /**
     * Set customer tax class ids
     *
     * @param int[] $customerTaxClassIds
     * @return $this
     */
    public function setCustomerTaxClassIds($customerTaxClassIds)
    {
        return $this->_set(TaxRule::CUSTOMER_TAX_CLASS_IDS, $customerTaxClassIds);
    }

    /**
     * Set product tax class ids
     *
     * @param int[] $productTaxClassIds
     * @return $this
     */
    public function setProductTaxClassIds($productTaxClassIds)
    {
        return $this->_set(TaxRule::PRODUCT_TAX_CLASS_IDS, $productTaxClassIds);
    }

    /**
     * Set product tax class ids
     *
     * @param int[] $taxRateIds
     * @return $this
     */
    public function setTaxRateIds($taxRateIds)
    {
        return $this->_set(TaxRule::TAX_RATE_IDS, $taxRateIds);
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->_set(TaxRule::PRIORITY, (int)$priority);
    }

    /**
     * Set sort order.
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->_set(TaxRule::SORT_ORDER, (int)$sortOrder);
    }

    /**
     * Set calculate subtotal.
     *
     * @param bool $calculateSubtotal
     * @return $this
     */
    public function setCalculateSubtotal($calculateSubtotal)
    {
        return $this->_set(TaxRule::CALCULATE_SUBTOTAL, (bool)$calculateSubtotal);
    }
}
