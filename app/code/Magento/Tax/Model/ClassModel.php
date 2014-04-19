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
 * @method \Magento\Tax\Model\Resource\TaxClass _getResource()
 * @method \Magento\Tax\Model\Resource\TaxClass getResource()
 * @method string getClassName()
 * @method \Magento\Tax\Model\ClassModel setClassName(string $value)
 * @method string getClassType()
 * @method \Magento\Tax\Model\ClassModel setClassType(string $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model;

class ClassModel extends \Magento\Framework\Model\AbstractModel
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
     * @var \Magento\Tax\Model\TaxClass\Factory
     */
    protected $_classFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Model\TaxClass\Factory $classFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Tax\Model\TaxClass\Factory $classFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_classFactory = $classFactory;
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Tax\Model\Resource\TaxClass');
    }

    /**
     * Check whether this class can be deleted
     *
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    public function checkClassCanBeDeleted()
    {
        if (!$this->getId()) {
            throw new \Magento\Framework\Model\Exception(__('This class no longer exists.'));
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'You cannot delete this tax class because it is used in Tax Rules. You have to delete the rules it is used in first.'
                )
            );
        }

        if ($typeModel->isAssignedToObjects()) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'You cannot delete this tax class because it is used in existing %1(s).',
                    $typeModel->getObjectTypeName()
                )
            );
        }

        return true;
    }
}
