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
 * @method Magento_Tax_Model_Resource_Class _getResource()
 * @method Magento_Tax_Model_Resource_Class getResource()
 * @method string getClassName()
 * @method Magento_Tax_Model_Class setClassName(string $value)
 * @method string getClassType()
 * @method Magento_Tax_Model_Class setClassType(string $value)
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
     * @var Magento_Tax_Model_Class_Factory
     */
    protected $_classFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Tax_Model_Class_Factory $classFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Tax_Model_Class_Factory $classFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_classFactory = $classFactory;
    }

    public function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_Class');
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
            throw new Magento_Core_Exception(__('This class no longer exists.'));
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            throw new Magento_Core_Exception(__('You cannot delete this tax class because it is used in Tax Rules. You have to delete the rules it is used in first.'));
        }

        $objectCount = $typeModel->getAssignedToObjects()->getSize();
        if ($objectCount > 0) {
            throw new Magento_Core_Exception(__('You cannot delete this tax class because it is used for %1 %2(s).', $objectCount, $typeModel->getObjectTypeName()));
        }

        return true;
    }
}
