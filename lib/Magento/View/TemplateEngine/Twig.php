<?php
/**
 * Template engine that enables Twig templates to be used for rendering.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\TemplateEngine;

use Magento\View\TemplateEngine\Twig as EngineTwig;

class Twig implements \Magento\View\TemplateEngine
{
    /**
     * @var EngineTwig\EnvironmentFactory
     */
    protected $factory;

    /**
     * @var EngineTwig\Extension
     */
    protected $extension;

    /**
     * @var EngineTwig\Environment
     */
    protected $environment;

    /**
     *  Populates the environment based on the environment builder provided.
     *
     * @param EngineTwig\EnvironmentFactory $factory
     * @param EngineTwig\Extension $extension
     */
    public function __construct(
        EngineTwig\EnvironmentFactory $factory,
        EngineTwig\Extension $extension
    ) {
        $this->factory = $factory;
        $this->extension = $extension;

        $this->extension->setBlockTracker($this);
    }

    /**
     * Render the named Twig template.
     *
     * @param string $fileName
     * @param array $dictionary
     * @throws Exception 
     * @return string
     */
    public function render($fileName, array $dictionary = array())
    {
        if ($this->environment === null) {
            $this->environment = $this->factory->create();
        }

        $output = $this->environment->render($fileName, $dictionary);

        return $output;
    }
}
