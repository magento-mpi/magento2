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
class View extends \Magento\App\Helper\AbstractHelper
{
    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface */
    protected $_customerMetadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService
    ) {
        $this->_customerMetadataService = $customerMetadataService;
        parent::__construct($context);
    }

    /**
     * Concatenate all customer name parts into full customer name.
     *
     * @param \Magento\Customer\Service\V1\Dto\Customer $customerData
     * @return string
     */
    public function getCustomerName(\Magento\Customer\Service\V1\Dto\Customer $customerData)
    {
        $name = '';
        $prefixMetadata = $this->_customerMetadataService->getAttributeMetadata('customer', 'prefix');
        if ($prefixMetadata->getIsVisible() && $customerData->getPrefix()) {
            $name .= $customerData->getPrefix() . ' ';
        }

        $name .= $customerData->getFirstname();

        $middleNameMetadata = $this->_customerMetadataService->getAttributeMetadata('customer', 'middlename');
        if ($middleNameMetadata->getIsVisible() && $customerData->getMiddlename()) {
            $name .= ' ' . $customerData->getMiddlename();
        }

        $name .=  ' ' . $customerData->getLastname();

        $suffixMetadata = $this->_customerMetadataService->getAttributeMetadata('customer', 'suffix');
        if ($suffixMetadata->getIsVisible() && $customerData->getSuffix()) {
            $name .= ' ' . $customerData->getSuffix();
        }
        return $name;
    }
}
