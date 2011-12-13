<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_PhpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $content = file_get_contents($file);
        $this->_testDeprecatedClasses($content);
        $this->_testDeprecatedMethods($content);
        $this->_testDeprecatedMethodArguments($content);
        $this->_testDeprecatedProperties($content);
        $this->_testDeprecatedActions($content);
        $this->_testDeprecatedConstants($content);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        $folders = array(
            'app',
            'dev',
            'downloader',
            'lib/Mage',
            'lib/Magento',
            'lib/Varien',
            'pub/errors'
        );

        $result = array();
        foreach ($folders as $folder) {
            $iterationFiles = $this->_getFiles($folder);
            $result = array_merge($result, $iterationFiles);
        }
        return $result;
    }

    /**
     * Returns all PHP-files in folder and its subfolders
     *
     * @param  $folder
     * @return array
     */
    protected function _getFiles($folder)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(PATH_TO_SOURCE_CODE . '/' . $folder)
        );
        $regexIterator = new RegexIterator($iterator, '/\.(?:php|phtml)$/');
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $file = (string)$fileInfo;
            if (realpath($file) == __FILE__) {
                continue;
            }
            $result[] = array($file);
        }
        return $result;
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedClasses($content)
    {
        $declarations = $this->_loadDeprecatedEntities('deprecated_classes.txt');
        foreach ($declarations as $declaration) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($declaration['entity'], '/') . '[^a-z\d_]/i',
                $content,
                "Deprecated class '{$declaration['entity']}' is used, {$declaration['suggestion']}."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedMethods($content)
    {
        $declarations = $this->_loadDeprecatedEntities('deprecated_methods.txt');
        foreach ($declarations as $declaration) {
            if (!$this->_isClassContextPermitted($declaration['class'], $content)) {
                continue;
            }
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($declaration['entity'], '/') . '\s*\(/i',
                $content,
                "Deprecated method '{$declaration['entity']}' is used, {$declaration['suggestion']}."
            );
        }
    }

    /**
     * Returns whether the content in required class context (i.e. $class or child of $class is defined there)
     *
     * @param string $class
     * @param string $content
     * @return bool
     */
    protected function _isClassContextPermitted($class, $content)
    {
        if (!$class) {
            return true;
        }
        $regexp = '/(class|extends)\s+' . preg_quote($class, '/') . '(\s|;)/';
        return preg_match($regexp, $content) > 0;
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedMethodArguments($content)
    {
        $deprecations = array(
            'getTypeInstance' => 'remove arguments, refactor code to treat returned type instance as a singleton',
        );
        foreach ($deprecations as $method => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($method, '/') . '\s*\(\s*[^\)]+/i',
                $content,
                "Method '$method' is called with deprecated arguments, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedProperties($content)
    {
        $declarations = $this->_loadDeprecatedEntities('deprecated_properties.txt');
        foreach ($declarations as $declaration) {
            if (!$this->_isClassContextPermitted($declaration['class'], $content)) {
                continue;
            }
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($declaration['entity'], '/') . '[^a-z\d_]/i',
                $content,
                "Deprecated property '{$declaration['entity']}' is used, {$declaration['suggestion']}."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedActions($content)
    {
        $deprecations = array(
            'catalog/product/image'
                => 'resizing images upon the client request has been deprecated, use server-side resizing instead',
        );
        foreach ($deprecations as $action => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_\/]' . preg_quote($action, '/') . '[^a-z\d_\/]/i',
                $content,
                "Deprecated action '$action' is used, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedConstants($content)
    {
        $declarations = $this->_loadDeprecatedEntities('deprecated_constants.txt');
        foreach ($declarations as $declaration) {
            if (!$this->_isClassContextPermitted($declaration['class'], $content)) {
                continue;
            }

            // Test that no constant is present
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($declaration['entity'], '/') . '[^a-z\d_]/i',
                $content,
                "Deprecated constant '{$declaration['entity']}' is used, {$declaration['suggestion']}."
            );
        }
    }

    /**
     * Loads deprecated entities from file, parses and returns them as array
     * Possible keys:
     * - 'entity' - actual entity loaded (method name, property name, etc.)
     * - 'suggestion' - suggestion for a user, when entity is found
     * - 'class' - may be set, when entity is allowed to be searched only in specific class context
     *
     * @param string $fileName
     * @return array
     */
    protected function _loadDeprecatedEntities($fileName)
    {
        $filePath = dirname(__FILE__) . '/_files/' . $fileName;
        $arr = file($filePath);
        $result = array();
        foreach ($arr as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $values = explode(' | ', $line);

            $entityValues = explode('::', $values[0]);
            if (count($entityValues) > 1) {
                $class = $entityValues[0];
                $entity = $entityValues[1];
            } else {
                $class = null;
                $entity = $entityValues[0];
            }

            $suggestion = isset($values[1]) ? $values[1] : null;
            if (!$suggestion) {
                $suggestion = 'remove it';
            }

            $result[] = array(
                'entity' => $entity,
                'suggestion' => $suggestion,
                'class' => $class
            );
        }
        return $result;
    }
}
