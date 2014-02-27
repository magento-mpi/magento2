<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\TaxClass\Type;
use Magento\Service\Data\AbstractObject;

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
     * @var \Magento\Customer\Service\V1\Data\FilterBuilder
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
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Model\Calculation\Rule $calculationRule,
        \Magento\Customer\Model\Group $modelCustomerGroup,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Customer\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelCustomerGroup = $modelCustomerGroup;
        $this->groupService = $groupService;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @deprecated In favor of getAssignedDataObjects. This function should be removed once all the implementations of
     * \Magento\Tax\Model\TaxClass\Type\TypeInterface::getAssignedToObjects are refactored to return Data Objects
     *
     * Get Customer Groups with this tax class
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getAssignedToObjects()
    {
        return $this->_modelCustomerGroup
            ->getCollection()
            ->addFieldToFilter('tax_class_id', $this->getId());
    }

    /**
     * Get Customer Groups Data Objects with this tax class
     *
     * @return AbstractObject[]
     */
    public function getAssignedDataObjects()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            $this->filterBuilder->setField('tax_class_id')->setValue($this->getId())->create()
        )->create();
        $result = $this->groupService->searchGroups($searchCriteria);
        return $result->getItems();
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
