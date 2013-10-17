<?php
/**
 * In-memory pool of all template engines available in the system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

class Pool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_engines;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $engines Format: array('<name>' => 'TemplateEngine\Class', ...)
     */
    public function __construct(\Magento\ObjectManager $objectManager, array $engines)
    {
        $this->_objectManager = $objectManager;
        $this->_engines = $engines;
    }

    /**
     * Retrieve a template engine instance by its unique name
     *
     * @param string $name
     * @return \Magento\Core\Model\TemplateEngine\EngineInterface
     * @throws \InvalidArgumentException If template engine doesn't exist
     * @throws \UnexpectedValueException If template engine doesn't implement the necessary interface
     */
    public function get($name)
    {
        if (!isset($this->_engines[$name])) {
            throw new \InvalidArgumentException("Unknown template engine '$name'.");
        }
        $engineClass = $this->_engines[$name];
        $engineInstance = $this->_objectManager->get($engineClass);
        if (!($engineInstance instanceof \Magento\Core\Model\TemplateEngine\EngineInterface)) {
            throw new \UnexpectedValueException("$engineClass has to implement the template engine interface.");
        }
        return $engineInstance;
    }
}
