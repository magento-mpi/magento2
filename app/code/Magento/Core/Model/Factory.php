<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

/**
 * Model object factory
 */
class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new model object
     *
     * @param string $model
     * @param array $data
     * @throws \InvalidArgumentException
     * @return AbstractModel
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false == $modelInstance instanceof \Magento\Model\AbstractModel) {
            throw new \InvalidArgumentException($model . ' is not instance of \Magento\Model\AbstractModel');
        }
        return $modelInstance;
    }
}
