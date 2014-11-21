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
 * @see \Magento\Tax\Service\V1\TaxClassServiceInterface
 */
interface TaxClassRepositoryInterface
{
    /**
     * Get a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return \Magento\Tax\Api\Data\TaxClassInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @see \Magento\Tax\Service\V1\TaxClassServiceInterface::getTaxClassId
     */
    public function get($taxClassId);

    /**
     * Retrieve tax classes which match a specific criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Tax\Api\Data\TaxRateSearchResultsInterface containing Data\TaxClassInterface
     * @throws \Magento\Framework\Exception\InputException
     * @see \Magento\Tax\Service\V1\TaxClassServiceInterface::searchTaxClass
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create a Tax Class
     *
     * @param \Magento\Tax\Api\Data\TaxClassInterface $taxClass
     * @return string id for the newly created Tax class
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Model\Exception
     * @see \Magento\Tax\Service\V1\TaxClassServiceInterface::updateTaxClass
     * @see \Magento\Tax\Service\V1\TaxClassServiceInterface::createTaxClass
     */
    public function save(\Magento\Tax\Api\Data\TaxClassInterface $taxClass);

    /**
     * Delete a tax class
     *
     * @param \Magento\Tax\Api\Data\TaxClassInterface $taxClass
     * @return bool True if the tax class was deleted, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magento\Tax\Api\Data\TaxClassInterface $taxClass);

    /**
     * Delete a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return bool True if the tax class was deleted, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @see \Magento\Tax\Service\V1\TaxClassServiceInterface::deleteTaxClass
     */
    public function deleteById($taxClassId);
}
