<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View;

use Magento\View\TemplateEngineFactory;

class TemplateEnginePool
{
    /**
     * @var TemplateEngineFactory
     */
    protected $factory;

    /**
     * @var \Magento\View\TemplateEngineInterface[]
     */
    protected $engines = array();

    /**
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
     * @return \Magento\View\TemplateEngineInterface
     */
    public function get($name)
    {
        if (!isset($this->engines[$name])) {
            $this->engines[$name] = $this->factory->create($name);
        }
        return $this->engines[$name];
    }
}
