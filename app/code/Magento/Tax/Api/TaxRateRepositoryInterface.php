<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api;

/**
 * @see \Magento\Tax\Service\V1\TaxRateServiceInterface
 */
interface TaxRateRepositoryInterface
{
    /**
     * Create or update tax rate
     *
     * @param \Magento\Tax\Api\Data\TaxRateInterface $taxRate
     * @return \Magento\Tax\Api\Data\TaxRateInterface
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Exception If something went wrong while creating the TaxRate.
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::createTaxRate
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::updateTaxRate
     */
    public function save(\Magento\Tax\Api\Data\TaxRateInterface $taxRate);

    /**
     * Get tax rate
     *
     * @param int $rateId
     * @return \Magento\Tax\Api\Data\TaxRateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::getTaxRate
     */
    public function get($rateId);

    /**
     * Delete tax rate
     *
     * @param int $rateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::deleteTaxRate
     */
    public function deleteById($rateId);

    /**
     * Search TaxRates
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Tax\Api\Data\TaxRateSearchResultsInterface containing Data\TaxRateInterface objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::searchTaxRates
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete tax rate
     *
     * @param int $rateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     * @see \Magento\Tax\Service\V1\TaxRateServiceInterface::deleteTaxRate
     */
    public function delete(\Magento\Tax\Api\Data\TaxRateInterface $taxRate);
}
