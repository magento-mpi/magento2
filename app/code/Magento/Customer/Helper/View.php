<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

/**
 * Customer helper for view.
 */
class View extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    protected $_customerMetadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService
    ) {
        $this->_customerMetadataService = $customerMetadataService;
        parent::__construct($context);
    }

    /**
     * Concatenate all customer name parts into full customer name.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerData
     * @return string
     */
    public function getCustomerName(\Magento\Customer\Api\Data\CustomerInterface $customerData)
    {
        $name = '';
        $prefixMetadata = $this->_customerMetadataService->getAttributeMetadata('prefix');
        if ($prefixMetadata->isVisible() && $customerData->getPrefix()) {
            $name .= $customerData->getPrefix() . ' ';
        }

        $name .= $customerData->getFirstname();

        $middleNameMetadata = $this->_customerMetadataService->getAttributeMetadata('middlename');
        if ($middleNameMetadata->isVisible() && $customerData->getMiddlename()) {
            $name .= ' ' . $customerData->getMiddlename();
        }

        $name .= ' ' . $customerData->getLastname();

        $suffixMetadata = $this->_customerMetadataService->getAttributeMetadata('suffix');
        if ($suffixMetadata->isVisible() && $customerData->getSuffix()) {
            $name .= ' ' . $customerData->getSuffix();
        }
        return $name;
    }
}
