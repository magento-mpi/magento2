<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler;
use Zend\Code\Scanner\FileScanner,
    Magento\Tools\Di\Definition\Reader;

class Directory {

    /**
     * @var array
     */
    protected $_processedClasses;

    /**
     * @var array
     */
    protected $_definitions;

    /**
     * @param string $path
     * @param Log\Log $log
     */
    public function compile($path, Log\Log $log)
    {
        $rdi = new \RecursiveDirectoryIterator(realpath($path));
        $recursiveIterator = new \RecursiveIteratorIterator($rdi,1);
        /** @var $item \SplFileInfo */
        foreach ($recursiveIterator as $item) {
           if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
                $fileScanner = new FileScanner($item->getRealPath());
                $classNames = $fileScanner->getClassNames();
                foreach ($classNames as $className) {
                    if (isset($this->_processedClasses[$className])) {
                        continue;
                    }
                    require_once $item->getRealPath();
                    try {
                        $signatureReader = new Reader();
                        $this->_definitions[$className] = $signatureReader->read($className);
                    } catch (\ReflectionException $e) {
                        $log->log(Log\Log::COMPILATION_ERROR, $className, $e->getMessage());
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
        return $this->_definitions;
    }
}
