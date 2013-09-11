<?php
/**
 * Configuration value backend model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Data;

class BackendModelPool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Config\Data\BackendModelInterface[]
     */
    protected $_pool;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get backend model instance
     *
     * @param string $model
     * @return \Magento\Core\Model\Config\Data\BackendModelInterface
     * @throws \InvalidArgumentException
     */
    public function get($model)
    {
        if (!isset($this->_pool[$model])) {
            $instance = $this->_objectManager->create($model);
            if (!($instance instanceof \Magento\Core\Model\Config\Data\BackendModelInterface)) {
                throw new \InvalidArgumentException(
                    $model . ' does not instance of \Magento\Core\Model\Config\Data\BackendModelInterface'
                );
            }
            $this->_pool[$model] = $instance;
        }
        return $this->_pool[$model];
    }
}
