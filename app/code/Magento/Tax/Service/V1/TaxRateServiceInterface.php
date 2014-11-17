<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

/**
 * @deprecated @see \Magento\Tax\Api\TaxRateInterface
 */
interface TaxRateServiceInterface
{
    /**
     * Create tax rate
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Exception If something went wrong while creating the TaxRate.
     * @see \Magento\Tax\Api\TaxRateInterface::save
     */
    public function createTaxRate(\Magento\Tax\Service\V1\Data\TaxRate $taxRate);

    /**
     * Get tax rate
     *
     * @param int $rateId
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Tax\Api\TaxRateInterface::get
     */
    public function getTaxRate($rateId);

    /**
     * Update given tax rate
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @return bool
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Magento\Framework\Exception\NoSuchEntityException If the TaxRate to update can't be found in the system.
     * @throws \Exception If something went wrong while performing the update.
     * @see \Magento\Tax\Api\TaxRateInterface::save
     */
    public function updateTaxRate(\Magento\Tax\Service\V1\Data\TaxRate $taxRate);

    /**
     * Delete tax rate
     *
     * @param int $rateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     * @see \Magento\Tax\Api\TaxRateInterface::deleteByIdentifier
     */
    public function deleteTaxRate($rateId);

    /**
     * Search TaxRates
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\TaxRateSearchResults containing Data\TaxRate objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @see \Magento\Tax\Api\TaxRateInterface::getList
     */
    public function searchTaxRates(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
