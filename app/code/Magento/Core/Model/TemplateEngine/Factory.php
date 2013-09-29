<?php
/**
 * Factory class for Template Engine
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

class Factory
{
    protected $_objectManager;

    /**
     * Template engine types
     */
    const ENGINE_TWIG = 'twig';
    const ENGINE_PHTML = 'phtml';

    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Gets the singleton instance of the appropriate template engine
     *
     * @param string $name
     * @return \Magento\Core\Model\TemplateEngine\EngineInterface
     * @throws \InvalidArgumentException if template engine doesn't exist
     */
    public function get($name)
    {
        if (self::ENGINE_TWIG == $name) {
            return $this->_objectManager->get('Magento\Core\Model\TemplateEngine\Twig');
        } else if (self::ENGINE_PHTML == $name) {
            return $this->_objectManager->get('Magento\Core\Model\TemplateEngine\Php');
        }
        // unknown type, throw exception
        throw new \InvalidArgumentException('Unknown template engine type: ' . $name);
    }
}
