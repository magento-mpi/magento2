<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

class Generator
{
    const GENERATION_SUCCESS = 'success';
    const GENERATION_ERROR = 'error';
    const GENERATION_SKIP = 'skip';

    /**
     * @var \Magento\Code\Generator\EntityAbstract
     */
    protected $_generator;

    /**
     * @var \Magento\Autoload\IncludePath
     */
    protected $_autoloader;

    /**
     * @var \Magento\Code\Generator\Io
     */
    protected $_ioObject;

    /**
     * @var string[]
     */
    protected $_generatedEntities;

    /**
     * @param Generator\EntityAbstract $generator
     * @param \Magento\Autoload\IncludePath $autoloader
     * @param Generator\Io $ioObject
     * @param \Magento\App\Filesystem $filesystem
     * @param array $generatedEntities
     */
    public function __construct(
        \Magento\Code\Generator\EntityAbstract $generator = null,
        \Magento\Autoload\IncludePath $autoloader = null,
        \Magento\Code\Generator\Io $ioObject = null,
        \Magento\App\Filesystem $filesystem = null,
        array $generatedEntities = array()
    ) {
        $this->_generator  = $generator;
        $this->_autoloader = $autoloader ? : new \Magento\Autoload\IncludePath();
        $this->_ioObject   = $ioObject ? : new \Magento\Code\Generator\Io(
            new \Magento\Filesystem\Driver\File(),
            $this->_autoloader
        );
        $this->_generatedEntities = $generatedEntities;
    }

    /**
     * Get generated entities
     *
     * @return string[]
     */
    public function getGeneratedEntities()
    {
        return $this->_generatedEntities;
    }

    /**
     * Generate Class
     *
     * @param string $className
     * @return string
     * @throws \Magento\Exception
     * @throws \InvalidArgumentException
     */
    public function generateClass($className)
    {
        // check if source class a generated entity
        $entity = null;
        $entityName = null;
        foreach ($this->_generatedEntities as $entityType => $generatorClass) {
            $entitySuffix = ucfirst($entityType);
            // if $className string ends on $entitySuffix substring
            if (strrpos($className, $entitySuffix) === strlen($className) - strlen($entitySuffix)) {
                $entity = $entityType;
                $entityName = rtrim(substr($className, 0, -1 * strlen($entitySuffix)),
                    \Magento\Autoload\IncludePath::NS_SEPARATOR);
                break;
            }
        }
        if (!$entity || !$entityName) {
            return self::GENERATION_ERROR;
        }

        // check if file already exists
        $autoloader = $this->_autoloader;
        if ($autoloader::getFile($className)) {
            return self::GENERATION_SKIP;
        }

        if (!$this->_generator) {
            // generate class file
            if (!isset($this->_generatedEntities[$entity])) {
                throw new \InvalidArgumentException('Unknown generation entity.');
            }
            $generatorClass = $this->_generatedEntities[$entity];
            $this->_generator = new $generatorClass($entityName, $className, $this->_ioObject);
        }
        if (!$this->_generator->generate()) {
            $errors = $this->_generator->getErrors();
            throw new \Magento\Exception(implode(' ', $errors));
        }

        // remove generator
        $this->_generator = null;

        return self::GENERATION_SUCCESS;
    }
}
