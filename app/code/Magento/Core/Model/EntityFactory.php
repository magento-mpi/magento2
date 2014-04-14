<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class EntityFactory implements \Magento\Framework\Data\Collection\EntityFactoryInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @throws \LogicException
     * @return \Magento\Object
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);
        //TODO: fix that when this factory used only for \Magento\Core\Model\Abstract
        //if (!$model instanceof \Magento\Core\Model\Abstract) {
        //    throw new \LogicException($className . ' doesn\'t implement \Magento\Core\Model\Abstract');
        //}
        return $model;
    }
}
