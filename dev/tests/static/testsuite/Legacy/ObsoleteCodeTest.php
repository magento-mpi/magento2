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

class Legacy_ObsoleteCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Message text that is used to render suggestions
     */
    const SUGGESTION_MESSAGE = 'Use "%s" instead.';

    /**
     * In-memory cache for the configuration files
     *
     * @var array
     */
    protected static $_configFilesCache = array();

    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content);
        $this->_testObsoleteMethods($content);
        $this->_testObsoleteMethodArguments($content);
        $this->_testObsoleteProperties($content);
        $this->_testObsoleteActions($content);
        $this->_testObsoleteConstants($content);
        $this->_testObsoletePropertySkipCalculate($content);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        return FileDataProvider::getPhpFiles();
    }

    /**
     * @param string $file
     * @dataProvider xmlFileDataProvider
     */
    public function testXmlFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content);
    }

    /**
     * @return array
     */
    public function xmlFileDataProvider()
    {
        return FileDataProvider::getXmlFiles();
    }

    /**
     * @param string $file
     * @dataProvider jsFileDataProvider
     */
    public function testJsFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoletePropertySkipCalculate($content);
    }

    /**
     * @return array
     */
    public function jsFileDataProvider()
    {
        return FileDataProvider::getJsFiles();
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteClasses($content)
    {
        $declarations = $this->_getRelevantConfigEntities('obsolete_classes*.php', $content);
        foreach ($declarations as $entity => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($entity, '/') . '[^a-z\d_]/iS',
                $content,
                "Class '$entity' is obsolete. $suggestion"
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteMethods($content)
    {
        $declarations = $this->_getRelevantConfigEntities('obsolete_methods*.php', $content);
        foreach ($declarations as $entity => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($entity, '/') . '\s*\(/iS',
                $content,
                "Method '$entity' is obsolete. $suggestion"
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteMethodArguments($content)
    {
        $suggestion = 'Remove arguments, refactor code to treat returned type instance as a singleton.';
        $this->assertNotRegExp(
            '/[^a-z\d_]getTypeInstance\s*\(\s*[^\)]+/iS',
            $content,
            "Method 'getTypeInstance' is called with obsolete arguments. $suggestion"
        );
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteProperties($content)
    {
        $declarations = $this->_getRelevantConfigEntities('obsolete_properties*.php', $content);
        foreach ($declarations as $entity => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($entity, '/') . '[^a-z\d_]/iS',
                $content,
                "Property '$entity' is obsolete. $suggestion"
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteActions($content)
    {
        $suggestion = 'Resizing images upon the client request is obsolete, use server-side resizing instead';
        $this->assertNotRegExp(
            '#[^a-z\d_/]catalog/product/image[^a-z\d_/]#iS',
            $content,
            "Action 'catalog/product/image' is obsolete. $suggestion"
        );
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteConstants($content)
    {
        $declarations = $this->_getRelevantConfigEntities('obsolete_constants*.php', $content);
        foreach ($declarations as $entity => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($entity, '/') . '[^a-z\d_]/iS',
                $content,
                "Constant '$entity' is obsolete. $suggestion"
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoletePropertySkipCalculate($content)
    {
        $this->assertNotRegExp(
            '/[^a-z\d_]skipCalculate[^a-z\d_]/iS',
            $content,
            "Configuration property 'skipCalculate' is obsolete."
        );
    }

    protected function _getRelevantConfigEntities($fileNamePattern, $content)
    {
        $result = array();
        foreach ($this->_loadConfigFiles($fileNamePattern) as $entity => $info) {
            $class = $info['class_scope'];
            $regexp = '/(class|extends)\s+' . preg_quote($class, '/') . '(\s|;)/S';
            /* Note: strpos is used just to prevent excessive preg_match calls */
            if ($class && (!strpos($content, $class) || !preg_match($regexp, $content))) {
                continue;
            }
            $result[$entity] = $info['suggestion'];
        }
        return $result;
    }

    /**
     * Loads obsolete entities from file, parses and returns them as array
     * Possible keys:
     * - 'entity' - actual entity loaded (method name, property name, etc.)
     * - 'suggestion' - suggestion for a user, when entity is found
     * - 'class_scope' - may be set, when entity is allowed to be searched only in specific class context
     *
     * @param string $fileNamePattern
     * @return array
     */
    protected function _loadConfigFiles($fileNamePattern)
    {
        if (isset(self::$_configFilesCache[$fileNamePattern])) {
            return self::$_configFilesCache[$fileNamePattern];
        }
        $config = array();
        foreach (glob(dirname(__FILE__) . '/_files/' . $fileNamePattern, GLOB_BRACE) as $configFile) {
            $config = array_merge($config, include($configFile));
        }
        $result = array();
        foreach ($config as $key => $value) {
            $entity = is_string($key) ? $key : $value;
            $class = isset($value['class_scope']) ? $value['class_scope'] : null;
            $suggestion = isset($value['suggestion']) ? sprintf(self::SUGGESTION_MESSAGE, $value['suggestion']) : '';
            $result[$entity] = array(
                'suggestion' => $suggestion,
                'class_scope' => $class
            );
        }
        self::$_configFilesCache[$fileNamePattern] = $result;
        return $result;
    }
}
