<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator;

use \Magento\Framework\Code\Generator;

class Autoloader
{
    /**
     * @var \Magento\Framework\Code\Generator
     */
    protected $_generator;

    /**
     * @var \Magento\Framework\Code\Generator\FileResolver
     */
    protected $fileResolver;
    
    /**
     * @param \Magento\Framework\Code\Generator $generator
     * @param \Magento\Framework\Code\Generator\FileResolver $fileResolver
     */
    public function __construct(
        \Magento\Framework\Code\Generator $generator,
        \Magento\Framework\Code\Generator\FileResolver $fileResolver
    ) {
        $this->fileResolver = $fileResolver;
        $this->_generator = $generator;
    }

    /**
     * Load specified class name and generate it if necessary
     *
     * @param string $className
     * @return void
     */
    public function load($className)
    {
        if (!class_exists($className)) {
            if (Generator::GENERATION_SUCCESS === $this->_generator->generateClass($className)) {
                $file = $this->fileResolver->getFile($className);
                if ($file) {
                    include $file;
                }
            }
        }
    }
}
