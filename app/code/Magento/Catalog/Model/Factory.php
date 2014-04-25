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
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create model
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Model\Exception
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);

        if (!$model instanceof \Magento\Framework\Model\AbstractModel) {
            throw new \Magento\Framework\Model\Exception($className . ' doesn\'t extends \Magento\Framework\Model\AbstractModel');
        }
        return $model;
    }
}
