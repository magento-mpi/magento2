<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler;
use \Zend\Code\Scanner\FileScanner,
    Magento\Tools\Di\Compiler\Log\Log;

class Directory
{
    /**
     * @var array
     */
    protected $_processedClasses = array();

    /**
     * @var array
     */
    protected $_definitions = array();

    /**
     * @var string
     */
    protected $_current;

    /**
     * @var Log
     */
    protected $_log;

    /**
     * @var array
     */
    protected $_relations;

    /**
     * @var  \Magento\Code\Validator
     */
    protected $_validator;

    /**
     * @param Log $log
     * @param \Magento\Code\Validator $validator
     */
    public function __construct(Log $log, \Magento\Code\Validator $validator)
    {
        $this->_log = $log;
        $this->_validator = $validator;
        set_error_handler(array($this, 'errorHandler'), E_STRICT);
    }

    /**
     * @param int $errno
     * @param string $errstr
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function errorHandler($errno, $errstr)
    {
        $this->_log->add(Log::COMPILATION_ERROR, $this->_current, $errstr);
    }

    /**
     * @param string $path
     */
    public function compile($path)
    {
        $rdi = new \RecursiveDirectoryIterator(realpath($path));
        $recursiveIterator = new \RecursiveIteratorIterator($rdi, 1);
        /** @var $item \SplFileInfo */
        foreach ($recursiveIterator as $item) {
            if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
                $fileScanner = new FileScanner($item->getRealPath());
                $classNames = $fileScanner->getClassNames();
                foreach ($classNames as $className) {
                    $this->_current = $className;
                    if (!class_exists($className)) {
                        require_once $item->getRealPath();
                    }
                    try {
                        $this->_validator->validate($className);
                        $signatureReader = new \Magento\Code\Reader\ClassReader();
                        $this->_definitions[$className] = $signatureReader->getConstructor($className);
                        $this->_relations[$className] = $signatureReader->getParents($className);
                    } catch (\Magento\Code\ValidationException $exception) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
                    } catch (\ReflectionException $e) {
                        $this->_log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
                    }
                    $this->_processedClasses[$className] = 1;
                }
            }
        }
    }

    /**
     * Retrieve compilation result
     *
     * @return array
     */
    public function getResult()
    {
        return array($this->_definitions, $this->_relations);
    }
}
