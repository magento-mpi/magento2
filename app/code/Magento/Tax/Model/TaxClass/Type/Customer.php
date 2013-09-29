<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Tax Class
 */
namespace Magento\Tax\Model\TaxClass\Type;

class Customer
    extends \Magento\Tax\Model\TaxClass\TypeAbstract
    implements \Magento\Tax\Model\TaxClass\Type\TypeInterface
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $_modelCustomerGroup;

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
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getAssignedToObjects()
    {
        return $this->_modelCustomerGroup
            ->getCollection()
            ->addFieldToFilter('tax_class_id', $this->getId());
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
