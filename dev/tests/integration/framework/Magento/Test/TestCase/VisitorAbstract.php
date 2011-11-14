<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * An ancestor class for tests, that visit many files, and where more visiting method can be added with time
 */
abstract class Magento_Test_TestCase_VisitorAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * List of methods in this class, that are designed to check file content.
     * Filled automatically via reflection.
     *
     * @var array
     */
    protected $_visitorMethods = null;

    /**
     * Returns all methods in this class, that are designed to visit the file content.
     * Protected methods starting with '_visit' are considered to be visitor methods.
     *
     * @return array
     */
    protected function _getVisitorMethods()
    {
        if ($this->_visitorMethods === null) {
            $this->_visitorMethods = array();
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
                if (substr($method->name, 0, 6) == '_visit') {
                    $this->_visitorMethods[] = $method->name;
                }
            }
        }

        return $this->_visitorMethods;
    }

    /**
     * Checks whether file path has required extension
     *
     * @param string|array $extensions
     * @return bool
     */
    protected function _fileHasExtensions($fileInfo, $extensions)
    {
        if (is_string($extensions)) {
            $extensions = array($extensions);
        }

        $fileExtension = pathinfo($fileInfo->getBasename(), PATHINFO_EXTENSION);
        $key = array_search($fileExtension, $extensions);
        return ($key !== false);
    }
}
