<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Type;

/**
 * Customer Tax Class
 */
class Customer extends \Magento\Tax\Model\TaxClass\AbstractType
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $_modelCustomerGroup;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var \Magento\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER;

    /**
     * @param \Magento\Tax\Model\Calculation\Rule $calculationRule
     * @param \Magento\Customer\Model\Group $modelCustomerGroup
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Model\Calculation\Rule $calculationRule,
        \Magento\Customer\Model\Group $modelCustomerGroup,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelCustomerGroup = $modelCustomerGroup;
    }

    /**
     * Get Customer Groups with this tax class
     *
     * @return int
     */
    public function getAssignedToObjectsSize()
    {
        return count($this->_modelCustomerGroup->getCollection()->addFieldToFilter('tax_class_id', $this->getId()));
    }

    /**
     * Get Name of Objects that use this Tax Class Type
     *
     * @return string
     */
    public function getObjectTypeName()
    {
        return __('customer group');
    }
}
