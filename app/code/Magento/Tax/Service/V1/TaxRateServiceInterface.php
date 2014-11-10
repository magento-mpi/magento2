<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

interface TaxRateServiceInterface
{
    /**
     * Create tax rate
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Exception If something went wrong while creating the TaxRate.
     */
    public function createTaxRate(\Magento\Tax\Service\V1\Data\TaxRate $taxRate);

    /**
     * Get tax rate
     *
     * @param int $rateId
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
     */
    public function updateTaxRate(\Magento\Tax\Service\V1\Data\TaxRate $taxRate);

    /**
     * Delete tax rate
     *
     * @param int $rateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function deleteTaxRate($rateId);

    /**
     * Search TaxRates
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\TaxRateSearchResults containing Data\TaxRate objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function searchTaxRates(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
