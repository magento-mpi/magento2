<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\Converter;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Validator\ValidatorException;

/**
 * Manipulate Customer Address Entities *
 */
class CustomerService implements CustomerServiceInterface
{
    /**
     * @var Converter
     */
    private $_converter;

    /**
     * Constructor
     *
     * @param Converter $converter
     */
    public function __construct(
        Converter $converter
    ) {
        $this->_converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerByEmail($customerEmail, $websiteId = null)
    {
        $customerModel = $this->_converter->getCustomerModelByEmail($customerEmail, $websiteId);
        return $this->_converter->createCustomerFromModel($customerModel);
    }
}
