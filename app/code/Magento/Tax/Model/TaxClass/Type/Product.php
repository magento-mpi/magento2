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
 * Product Tax Class
 */
namespace Magento\Tax\Model\TaxClass\Type;

class Product
    extends \Magento\Tax\Model\TaxClass\AbstractType
    implements \Magento\Tax\Model\TaxClass\Type\TypeInterface
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_modelProduct;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT;

    /**
     * @param \Magento\Tax\Model\Calculation\Rule $calculationRule
     * @param \Magento\Catalog\Model\Product $modelProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Model\Calculation\Rule $calculationRule,
        \Magento\Catalog\Model\Product $modelProduct,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelProduct = $modelProduct;
    }

    /**
     * Get Products with this tax class
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getAssignedToObjects()
    {
        return $this->_modelProduct
            ->getCollection()
            ->addAttributeToFilter('tax_class_id', $this->getId());
    }

    /**
     * Get Name of Objects that use this Tax Class Type
     *
     * @return string
     */
    public function getObjectTypeName()
    {
        return __('product');
    }
}
