<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model;

/**
 * Persistent Factory
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates models
     *
     * @param string $className
     * @param array $data
     * @return mixed
     */
    public function create($className, $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}
