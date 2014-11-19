<?php
/**
 * Config data Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config\Data;

class ProcessorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProcessorInterface[]
     */
    protected $_pool;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get concrete Processor Interface instance
     *
     * @param string $model
     * @return ProcessorInterface
     * @throws \InvalidArgumentException
     */
    public function get($model)
    {
        if (!isset($this->_pool[$model])) {
            $instance = $this->_objectManager->create($model);
            if (!$instance instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    $model . ' does not instance of \Magento\Framework\App\Config\Data\ProcessorInterface'
                );
            }
            $this->_pool[$model] = $instance;
        }
        return $this->_pool[$model];
    }
}
