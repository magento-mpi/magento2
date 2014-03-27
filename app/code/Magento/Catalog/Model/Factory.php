<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Model factory
 */
namespace Magento\Catalog\Model;

class Factory
{
    /**
     * Object Manager
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
     * Create model
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Model\AbstractModel
     * @throws \Magento\Model\Exception
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);

        if (!$model instanceof \Magento\Model\AbstractModel) {
            throw new \Magento\Model\Exception($className . ' doesn\'t extends \Magento\Model\AbstractModel');
        }
        return $model;
    }
}
