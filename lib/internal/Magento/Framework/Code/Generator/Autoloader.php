<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator;

use \Magento\Framework\Code\Generator;
use \Magento\Framework\Autoload\AutoloaderRegistry;
use \Magento\Framework\Autoload\AutoloaderInterface;

class Autoloader
{
    /**
     * @var \Magento\Framework\Code\Generator
     */
    protected $_generator;

    /**
     * @param \Magento\Framework\Code\Generator $generator
     */
    public function __construct(
        \Magento\Framework\Code\Generator $generator
    ) {
        $this->_generator = $generator;
    }

    /**
     * Load specified class name and generate it if necessary
     *
     * @param string $className
     * @return bool True if class was loaded
     */
    public function load($className)
    {
        if (!class_exists($className)) {
            return Generator::GENERATION_ERROR != $this->_generator->generateClass($className);
        }
        return true;
    }
}
