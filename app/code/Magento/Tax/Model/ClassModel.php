<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Tax class model
 *
 * @method \Magento\Tax\Model\Resource\TaxClass _getResource()
 * @method \Magento\Tax\Model\Resource\TaxClass getResource()
 * @method string getClassName()
 * @method \Magento\Tax\Model\ClassModel setClassName(string $value)
 * @method string getClassType()
 * @method \Magento\Tax\Model\ClassModel setClassType(string $value)
 */
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
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Model\TaxClass\Factory $classFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
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
     * @throws CouldNotDeleteException
     */
    protected function checkClassCanBeDeleted()
    {
        if (!$this->getId()) {
            throw new CouldNotDeleteException('This class no longer exists.');
        }

        $typeModel = $this->_classFactory->create($this);

        if ($typeModel->getAssignedToRules()->getSize() > 0) {
            throw new CouldNotDeleteException(
                'You cannot delete this tax class because it is used in Tax Rules.' .
                ' You have to delete the rules it is used in first.'
            );
        }

        if ($typeModel->isAssignedToObjects()) {
            throw new CouldNotDeleteException(
                'You cannot delete this tax class because it is used in existing %object(s).',
                ['object' => $typeModel->getObjectTypeName()]
            );
        }

        return true;
    }

    /**
     * Validate tax class can be deleted
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeDelete()
    {
        $this->checkClassCanBeDeleted();
        return parent::beforeDelete();
    }
}
