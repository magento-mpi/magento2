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
 * @deprecated @see \Magento\Tax\Api\TaxClassRepositoryInterface
 */
interface TaxClassServiceInterface
{
    /**#@+
     * Tax class type.
     */
    const TYPE_CUSTOMER = 'CUSTOMER';
    const TYPE_PRODUCT = 'PRODUCT';
    /**#@-*/

    /**
     * Create a Tax Class
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClass $taxClass
     * @return string id for the newly created Tax class
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Model\Exception
     * @see \Magento\Tax\Api\TaxClassRepositoryInterface::save
     */
    public function createTaxClass(\Magento\Tax\Service\V1\Data\TaxClass $taxClass);

    /**
     * Get a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return \Magento\Tax\Service\V1\Data\TaxClass
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @see \Magento\Tax\Api\TaxClassRepositoryInterface::get
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
     * @see \Magento\Tax\Api\TaxClassRepositoryInterface::save
     */
    public function updateTaxClass($taxClassId, \Magento\Tax\Service\V1\Data\TaxClass $taxClass);

    /**
     * Delete a tax class with the given tax class id.
     *
     * @param int $taxClassId
     * @return bool True if the tax class was deleted, false otherwise
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @see \Magento\Tax\Api\TaxClassRepositoryInterface::deleteByIdentifier
     */
    public function deleteTaxClass($taxClassId);

    /**
     * Retrieve tax classes which match a specific criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\TaxClassSearchResults containing Data\TaxClass
     * @throws \Magento\Framework\Exception\InputException
     * @see \Magento\Tax\Api\TaxClassRepositoryInterface::getList
     */
    public function searchTaxClass(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Get tax class id
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClassKey|null $taxClassKey
     * @param string $taxClassType
     * @return int|null
     * @see \Magento\Tax\Api\TaxClassManagementInterface::getTaxClassId
     */
    public function getTaxClassId($taxClassKey, $taxClassType = self::TYPE_PRODUCT);
}
