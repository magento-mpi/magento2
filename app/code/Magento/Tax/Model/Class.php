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
 * Tax class model
 *
 * @method Magento_Tax_Model_Resource_TaxClass _getResource()
 * @method Magento_Tax_Model_Resource_TaxClass getResource()
 * @method string getClassName()
 * @method Magento_Tax_Model_Class setClassName(string $value)
 * @method string getClassType()
 * @method Magento_Tax_Model_Class setClassType(string $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tax_Model_Class extends Magento_Core_Model_Abstract
{
    /**
     * Defines Customer Tax Class string
     */
    const TAX_CLASS_TYPE_CUSTOMER = 'CUSTOMER';

    /**
     * Defines Product Tax Class string
     */
    const TAX_CLASS_TYPE_PRODUCT = 'PRODUCT';

    /**
     * @var Magento_Tax_Model_TaxClass_Factory
     */
    protected $_classFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param Magento_Tax_Model_TaxClass_Factory $classFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Tax_Model_TaxClass_Factory $classFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_classFactory = $classFactory;
    }

    public function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_TaxClass');
    }

    /**
     * Check whether this class can be deleted
     *
     * @return bool
     * @throws Magento_Core_Exception
     */
    public function checkClassCanBeDeleted()
    {
        if (!$this->getId()) {
            Mage::throwException(__('This class no longer exists.'));
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            Mage::throwException(__('You cannot delete this tax class because it is used in Tax Rules. You have to delete the rules it is used in first.'));
        }

        $objectCount = $typeModel->getAssignedToObjects()->getSize();
        if ($objectCount > 0) {
            Mage::throwException(__('You cannot delete this tax class because it is used for %1 %2(s).', $objectCount, $typeModel->getObjectTypeName()));
        }

        return true;
    }
}
