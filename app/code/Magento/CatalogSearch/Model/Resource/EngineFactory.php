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
class Magento_CatalogSearch_Model_Resource_EngineFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get engine instance
     *
     * @param string $className
     * @param array $arguments
     * @return Magento_CatalogSearch_Model_Resource_EngineInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $engine = $this->_objectManager->create($className, $arguments);

        if (false === ($engine instanceof Magento_CatalogSearch_Model_Resource_EngineInterface)) {
            throw new LogicException(
                $className . ' doesn\'t implement Magento_CatalogSearch_Model_Resource_EngineInterface'
            );
        }

        return $engine;
    }
}
