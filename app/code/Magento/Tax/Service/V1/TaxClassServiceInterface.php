<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Service\V1\Data\TaxClass as TaxClassDataObject;

/**
 * Interface for tax class service.
 */
interface TaxClassServiceInterface
{
    /**
     * Create a Tax Class
     *
     * @param TaxClassDataObject $taxClass
     * @return TaxClassDataObject
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     */
    public function createTaxClass(TaxClassDataObject $taxClass);

    /**
     * Update a tax class with the given information.
     *
     * @param TaxClassDataObject $taxClass
     * @return bool True if the tax class was updated, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with given tax class ID does not exist
     * @throws \Magento\Framework\Exception\InputException
     */
    public function updateTaxClass(TaxClassDataObject $taxClass);

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
     * @return \Magento\Tax\Service\V1\Data\SearchResults
     * @throws \Magento\Framework\Exception\InputException
     */
    public function searchTaxClass(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
