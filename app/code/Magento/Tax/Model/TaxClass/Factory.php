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
 * Tax Class factory
 */
namespace Magento\Tax\Model\TaxClass;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Type to class map
     *
     * @var array
     */
    protected $_types = array(
        \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER => 'Magento\Tax\Model\TaxClass\Type\Customer',
        \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT => 'Magento\Tax\Model\TaxClass\Type\Product',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param \Magento\Tax\Model\ClassModel $taxClass
     * @return \Magento\Tax\Model\TaxClass\Type\TypeInterface
     * @throws \Magento\Core\Exception
     */
    public function create(\Magento\Tax\Model\ClassModel $taxClass)
    {
        $taxClassType = $taxClass->getClassType();
        if (!array_key_exists($taxClassType, $this->_types)) {
            throw new \Magento\Core\Exception(sprintf('Invalid type of tax class "%s"', $taxClassType));
        }
        return $this->_objectManager->create(
            $this->_types[$taxClassType],
            array('data' => array('id' => $taxClass->getId()))
        );
    }
}
