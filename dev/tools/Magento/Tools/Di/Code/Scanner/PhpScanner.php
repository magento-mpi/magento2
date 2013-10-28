<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

use \Magento\Tools\Di\Compiler\Log\Log;

class PhpScanner implements ScannerInterface
{
    /**
     * @var Log $log
     */
    protected $_log;

    /**
     * @param Log $log
     */
    public function __construct(Log $log)
    {
        $this->_log = $log;
    }

    /**
     * Get array of class names
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = array();
        foreach ($files as $file) {
            $classes = $this->_getDeclaredClasses($file);
            foreach ($classes as $className) {
                $reflectionClass = new \ReflectionClass($className);
                if ($reflectionClass->hasMethod('__construct')) {
                    $constructor = $reflectionClass->getMethod('__construct');
                    $parameters = $constructor->getParameters();
                    /** @var $parameter \ReflectionParameter */
                    foreach ($parameters as $parameter) {
                        preg_match('/\[\s\<\w+?>\s([\w\\\\]+)/s', $parameter->__toString(), $matches);
                        if (isset($matches[1]) && substr($matches[1], -7) == 'Factory') {
                            $factoryClassName = $matches[1];
                            if (class_exists($factoryClassName)) {
                                continue;
                            }
                            $entityName = rtrim(substr($factoryClassName, 0, -7), '\\');
                            if (!class_exists($entityName)) {
                                $this->_log->add(
                                    Log::CONFIGURATION_ERROR,
                                    $factoryClassName,
                                    'Invalid Factory for nonexistent class ' . $entityName . ' in file ' . $file
                                );
                                continue;
                            }

                            if (substr($factoryClassName, -8) == '\\Factory') {
                                $this->_log->add(
                                    Log::CONFIGURATION_ERROR,
                                    $factoryClassName,
                                    'Invalid Factory declaration for class ' . $entityName . ' in file ' . $file
                                );
                                continue;
                            }
                            $output[] = $factoryClassName;
                        }
                    }
                }
            }
        }
        return array_unique($output);
    }

    /**
     * Get classes declared in the file
     *
     * @param string $file
     * @return array
     */
    protected function _getDeclaredClasses($file)
    {
        $classes = array ();
        $namespace = "";
        $tokens = token_get_all(file_get_contents($file));
        $count = count($tokens);

        for ($tokenIterator = 0; $tokenIterator < $count; $tokenIterator ++) {
            if ($tokens[$tokenIterator][0] === T_NAMESPACE) {
                for ($tokenOffset = $tokenIterator + 1; $tokenOffset < $count; ++$tokenOffset) {
                    if ($tokens[$tokenOffset][0] === T_STRING) {
                        $namespace .= "\\" . $tokens[$tokenOffset][1];
                    } elseif ($tokens[$tokenOffset] === '{' || $tokens[$tokenOffset] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$tokenIterator][0] === T_CLASS) {
                for ($tokenOffset = $tokenIterator + 1; $tokenOffset < $count; ++$tokenOffset) {
                    if ($tokens[$tokenOffset] === '{') {
                        $classes[]= $namespace . "\\" . $tokens[$tokenIterator + 2][1];
                    }
                }

            }
        }
        return array_unique($classes);
    }
}
