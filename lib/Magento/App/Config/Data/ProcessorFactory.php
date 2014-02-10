<?php
/**
 * Config data Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Data;

class ProcessorFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\Config\Data\ProcessorInterface[]
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
     * Get concrete Processor Interface instance
     *
     * @param string $model
     * @return \Magento\App\Config\Data\ProcessorInterface
     * @throws \InvalidArgumentException
     */
    public function get($model)
    {
        if (!isset($this->_pool[$model])) {
            $instance = $this->_objectManager->create($model);
            if (!($instance instanceof \Magento\App\Config\Data\ProcessorInterface)) {
                throw new \InvalidArgumentException(
                    $model . ' does not instance of \Magento\App\Config\Data\ProcessorInterface'
                );
            }
            $this->_pool[$model] = $instance;
        }
        return $this->_pool[$model];
    }
}
