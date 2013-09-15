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
 * @method \Magento\Tax\Model\Resource\Class _getResource()
 * @method \Magento\Tax\Model\Resource\Class getResource()
 * @method string getClassName()
 * @method Magento_Tax_Model_Class setClassName(string $value)
 * @method string getClassType()
 * @method Magento_Tax_Model_Class setClassType(string $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Tax\Model;

class ClassModel extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\Tax\Model\Class\Factory
     */
    protected $_classFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Model\Class\Factory $classFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Model\Class\Factory $classFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_classFactory = $classFactory;
    }

    public function _construct()
    {
        $this->_init('Magento\Tax\Model\Resource\Class');
    }

    /**
     * Check whether this class can be deleted
     *
     * @return bool
     * @throws \Magento\Core\Exception
     */
    public function checkClassCanBeDeleted()
    {
        if (!$this->getId()) {
            \Mage::throwException(__('This class no longer exists.'));
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            \Mage::throwException(__('You cannot delete this tax class because it is used in Tax Rules. You have to delete the rules it is used in first.'));
        }

        $objectCount = $typeModel->getAssignedToObjects()->getSize();
        if ($objectCount > 0) {
            \Mage::throwException(__('You cannot delete this tax class because it is used for %1 %2(s).', $objectCount, $typeModel->getObjectTypeName()));
        }

        return true;
    }
}
