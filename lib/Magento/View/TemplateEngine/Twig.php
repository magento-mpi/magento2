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

use Magento\View\TemplateEngine;

class Twig implements TemplateEngine
{
    /**
     * @var Magento_View_TemplateEngine_Twig_EnvironmentFactory
     */
    protected $factory;

    /**
     * @var Magento_View_TemplateEngine_Twig_Extension
     */
    protected $extension;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     *  Populates the environment based on the environment builder provided.
     *
     * @param Magento_View_TemplateEngine_Twig_EnvironmentFactory $factory
     * @param Magento_View_TemplateEngine_Twig_Extension $extension
     */
    public function __construct(
        Magento_View_TemplateEngine_Twig_EnvironmentFactory $factory,
        Magento_View_TemplateEngine_Twig_Extension $extension
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
