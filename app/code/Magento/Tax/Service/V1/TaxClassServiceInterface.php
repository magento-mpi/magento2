<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

/**
 * Interface for tax class service.
 */
interface TaxClassServiceInterface
{
    /**
     * Create a Tax Class
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClass $taxClass
     * @return string id for the newly created Tax class
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Model\Exception
     */
    public function createTaxClass(\Magento\Tax\Service\V1\Data\TaxClass $taxClass);

    /**
     * Get a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return \Magento\Tax\Service\V1\Data\TaxClass
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     */
    public function getTaxClass($taxClassId);

    /**
     * Update a tax class with the given information.
     *
     * @param int $taxClassId
     * @param \Magento\Tax\Service\V1\Data\TaxClass $taxClass
     * @return bool True if the tax class was updated, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with given tax class ID does not exist
     * @throws \Magento\Framework\Exception\InputException
     */
    public function updateTaxClass($taxClassId, \Magento\Tax\Service\V1\Data\TaxClass $taxClass);

    /**
     * Delete a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return bool True if the tax class was deleted, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     */
    public function deleteTaxClass($taxClassId);

    /**
     * Retrieve tax classes which match a specific criteria.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\TaxClassSearchResults containing Data\TaxClass
     * @throws \Magento\Framework\Exception\InputException
     */
    public function searchTaxClass(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
