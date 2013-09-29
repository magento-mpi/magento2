<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Collection;

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
     * @param string $className
     * @param array $data
     * @return \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $instance = $this->_objectManager->create($className, $data);

        if (!($instance instanceof \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection)) {
            throw new \InvalidArgumentException(
                $className . ' does not implement \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection'
            );
        }
        return $instance;
    }
}
