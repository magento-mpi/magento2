<?php
/**
 * Factory class for Template Engine
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_View
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
    protected $_objectManager;

    /**
     * Template engine types
     */
    const ENGINE_TWIG = 'twig';
    const ENGINE_PHTML = 'phtml';

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
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
            return $this->_objectManager->get('Magento\\View\\TemplateEngine\\Twig');
        } else if (self::ENGINE_PHTML == $name) {
            return $this->_objectManager->get('Magento\\View\\TemplateEngine\\Php');
        }
        // unknown type, throw exception
        throw new \InvalidArgumentException('Unknown template engine type: ' . $name);
    }
}
