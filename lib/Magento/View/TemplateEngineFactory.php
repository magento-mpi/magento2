<?php
/**
 * Factory class for Template Engine
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\ObjectManager;
use Magento\View\TemplateEngine;

class TemplateEngineFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**#@+
     * Template engine types
     */
    const ENGINE_TWIG = 'twig';
    const ENGINE_PHTML = 'phtml';
    /**#@-*/

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Gets the singleton instance of the appropriate template engine
     *
     * @param string $name
     * @return TemplateEngine
     * @throws \InvalidArgumentException if template engine doesn't exist
     */
    public function get($name)
    {
        if (self::ENGINE_TWIG == $name) {
            return $this->objectManager->get('Magento\\View\\TemplateEngine\\Twig');
        } elseif (self::ENGINE_PHTML == $name) {
            return $this->objectManager->get('Magento\\View\\TemplateEngine\\Php');
        }
        // Unknown type, throw exception
        throw new \InvalidArgumentException('Unknown template engine type: ' . $name);
    }
}
