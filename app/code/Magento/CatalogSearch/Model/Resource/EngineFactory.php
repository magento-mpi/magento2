<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Catalogsearch
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

        if (false === ($engine instanceof \Magento\CatalogSearch\Model\Resource\EngineInterface)) {
            throw new \LogicException(
                $className . ' doesn\'t implement \Magento\CatalogSearch\Model\Resource\EngineInterface'
            );
        }

        return $engine;
    }
}
