<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalogsearch engine factory
 */
namespace Magento\CatalogSearch\Model\Resource;

class EngineFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get engine instance
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\CatalogSearch\Model\Resource\EngineInterface
     * @throws \LogicException
     */
    public function create($className, array $arguments = array())
    {
        $engine = $this->_objectManager->create($className, $arguments);

        if (false === $engine instanceof \Magento\CatalogSearch\Model\Resource\EngineInterface) {
            throw new \LogicException(
                $className . ' doesn\'t implement \Magento\CatalogSearch\Model\Resource\EngineInterface'
            );
        }

        return $engine;
    }
}
