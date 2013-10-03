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
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Magento\Core\Exception
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);

        if (!$model instanceof \Magento\Core\Model\AbstractModel) {
            throw new \Magento\Core\Exception($className
                . ' doesn\'t extends \Magento\Core\Model\AbstractModel');
        }
        return $model;
    }
}
