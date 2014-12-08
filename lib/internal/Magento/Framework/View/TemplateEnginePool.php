<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;


class TemplateEnginePool
{
    /**
     * Factory
     *
     * @var TemplateEngineFactory
     */
    protected $factory;

    /**
     * Template engines
     *
     * @var \Magento\Framework\View\TemplateEngineInterface[]
     */
    protected $engines = [];

    /**
     * Constructor
     *
     * @param TemplateEngineFactory $factory
     */
    public function __construct(TemplateEngineFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Retrieve a template engine instance by its unique name
     *
     * @param string $name
     * @return \Magento\Framework\View\TemplateEngineInterface
     */
    public function get($name)
    {
        if (!isset($this->engines[$name])) {
            $this->engines[$name] = $this->factory->create($name);
        }
        return $this->engines[$name];
    }
}
