<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax class model
 *
 * @method Mage_Tax_Model_Resource_Class _getResource()
 * @method Mage_Tax_Model_Resource_Class getResource()
 * @method string getClassName()
 * @method Mage_Tax_Model_Class setClassName(string $value)
 * @method string getClassType()
 * @method Mage_Tax_Model_Class setClassType(string $value)
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Class extends Magento_Core_Model_Abstract
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
     * @var Mage_Tax_Model_Class_Factory
     */
    protected $_classFactory;

    /**
     * @var Mage_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Mage_Tax_Model_Class_Factory $classFactory
     * @param Mage_Tax_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Mage_Tax_Model_Class_Factory $classFactory,
        Mage_Tax_Helper_Data $helper,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_classFactory = $classFactory;
        $this->_helper = $helper;
    }

    public function _construct()
    {
        $this->_init('Mage_Tax_Model_Resource_Class');
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
            Mage::throwException($this->_helper->__('This class no longer exists.'));
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            Mage::throwException($this->_helper->__('You cannot delete this tax class because it is used in Tax Rules. You have to delete the rules it is used in first.'));
        }

        $objectCount = $typeModel->getAssignedToObjects()->getSize();
        if ($objectCount > 0) {
            Mage::throwException($this->_helper->__('You cannot delete this tax class because it is used for %d %s(s).', $objectCount, $typeModel->getObjectTypeName()));
        }

        return true;
    }
}
