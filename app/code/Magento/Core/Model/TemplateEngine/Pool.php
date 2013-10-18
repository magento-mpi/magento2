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
     * @var Factory
     */
    protected $_factory;

    /**
     * @var EngineInterface[]
     */
    protected $_engines = array();

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * Retrieve a template engine instance by its unique name
     *
     * @param string $name
     * @return EngineInterface
     */
    public function get($name)
    {
        if (!isset($this->_engines[$name])) {
            $this->_engines[$name] = $this->_factory->create($name);
        }
        return $this->_engines[$name];
    }
}
