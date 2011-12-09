<?php
/**
 * Scan source code for references to classes and see if they indeed exist
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_magentoRoot = '';

    /**
     * List of already found classes to avoid checking them over and over again
     *
     * @var array
     */
    protected static $_existingClasses = array();

    /**
     * @param SplFileInfo $file
     * @dataProvider phpCodeDataProvider
     */
    public function testPhpCode($file)
    {
        $contents = file_get_contents($file);
        $classes = $this->_collectMatches($contents, '/
            # ::getResourceModel ::getBlockSingleton ::getModel ::getSingleton
            \:\:get(?:Resource | Block)?(?:Model | Singleton)\(\s*[\'"]([a-z\d_]+)[\'"]\s*[\),]

            # various methods, first argument
            | \->(?:initReport | addBlock | createBlock | setDataHelperName | getBlockClassName _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*\'([a-z\d_]+)\'\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+?,\s*\'([a-z\d_]+)\'\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*\'([a-z\d_]+)\'\s*\)

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d_]+)[\'"]
            | \'resource_model\'\s*=>\s*[\'"]([a-z\d_]+)[\'"]
            | _parentResourceModelName\s*=\s*\'([a-z\d_]+)\'
            /imx'
        );

        // without modifier "i". Starting from capital letter is a significant characteristic of a class name
        $this->_collectMatches($contents, '/\->(?:_init|setType)\(\s*(?:,?\'([A-Z][a-z\d][A-Za-z\d_]+)\'){1,2}\s*\)/m',
            $classes
        );

        // special case for resource helpers
        $matches = $this->_collectMatches($contents, '/(?:\:\:|\->)getResourceHelper\(\s*\'([a-z\d_]+)\'\s*\)/imx');
        foreach ($matches as $moduleName) {
            $classes[] = "{$moduleName}_Model_Resource_Helper_Mysql4";
            $classes[] = "{$moduleName}_Model_Resource_Helper_Mssql";
            $classes[] = "{$moduleName}_Model_Resource_Helper_Oracle";
        }

        $this->_assertClassesExist($classes);
    }

    /**
     * @return array
     */
    public function phpCodeDataProvider()
    {
        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            self::getMagentoRoot(), FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
        ));
        $regexIterator = new RegexIterator($recursiveIterator,
            '#(app/(bootstrap|Mage)\.php | app/code/.+\.(php|phtml) | app/design/.+\.phtml | pub/[a-z]+\.php)$#x'
        );
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $result[] = array((string)$fileInfo);
        }
        return $result;
    }

    /**
     * @param string $path
     * @dataProvider configurationDataProvider
     */
    public function testConfiguration($path)
    {
        $xml = simplexml_load_file($path);
        $classes = array();

        // various nodes
        $nodes = $xml->xpath('/config//resource_adapter'
            . ' | //class | //model | //backend_model | //source_model | //price_model | //model_token'
            . ' | //attribute_model | //writer_model | //clone_model | //frontend_model'
        );
        foreach ($nodes as $node) {
            if (preg_match('/^([A-Z][a-z\d_][A-Za-z\d_]+)\:?/', (string)$node, $matches)) {
                $classes[$matches[1]] = 1;
            }
        }

        // "backend_model" attribute
        $nodes = $xml->xpath('//@backend_model');
        foreach ($nodes as $node) {
            $node = (array)$node;
            $classes[$node['@attributes']['backend_model']] = 1;
        }

        // special case -- logging "expected models"
        $nodes = $xml->xpath('/logging/*/expected_models/* | /logging/*/actions/*/expected_models/*');
        foreach ($nodes as $node) {
            $classes[$node->getName()] = 1;
        }

        $this->_assertClassesExist(array_keys($classes));
    }

    /**
     * @return array
     */
    public function configurationDataProvider()
    {
        $result = array();
        $excludedFiles = array('wsdl.xml', 'wsdl2.xml', 'wsi.xml');
        $globPattern = self::getMagentoRoot() . '/app/code/{community,core,local}/*/*/etc/*.xml';
        foreach (glob($globPattern, GLOB_BRACE) as $path) {
            if (in_array(basename($path), $excludedFiles)) {
                continue;
            }
            $result[] = array($path);
        }
        return $result;
    }

    /**
     * @param string $path
     * @dataProvider viewXmlDataProvider
     */
    public function testLayouts($path)
    {
        $xml = simplexml_load_file($path);
        $classes = array();

        // any text nodes that contain conventional block/model/helper names
        foreach ($xml->xpath('/layout//*[contains(text(),"_Block_")] | /layout//*[contains(text(),"_Model_")]
            | /layout//*[contains(text(),"_Helper_")]') as $class
        ) {
            $classes[(string)$class] = 1;
        }

        // block names in various attributes
        $nodes = $xml->xpath('/layout//@type | /layout//@attributeType | /layout//@name | /layout//@content'
            . ' | /layout//@render | /layout//@admin_renderer | /layout//@block | /layout//@renderer_block'
            . ' | /layout//@renderer'
        );
        foreach ($nodes as $node) {
            $node = (array)$node;
            foreach ($node['@attributes'] as $class) {
                if (false !== strpos($class, '_Block_')) {
                    $classes[(string)$class] = 1;
                }
            }
        }

        // helpers and modules
        foreach ($xml->xpath('/layout//@helper | /layout//@module') as $node) {
            $node = (array)$node;
            if (isset($node['@attributes']['helper'])) {
                $class = explode('::', $node['@attributes']['helper']);
                $classes[array_shift($class)] = 1;
            }
            if (isset($node['@attributes']['module'])) {
                $class = $node['@attributes']['module'] . '_Helper_Data';
                $classes[$class] = 1;
            }
        }

        $this->_assertClassesExist(array_keys($classes));
    }

    /**
     * Find XML-files of view layer
     *
     * @return array
     */
    public static function viewXmlDataProvider()
    {
        $result = array();
        $root = self::getMagentoRoot();
        $globPatterns = array(
            "{$root}/app/code/{community,core,local}/*/*/view/*.xml",
            "{$root}/app/design/*/*/*/*.xml",
            // diving 2-3 levels should be enough and that's faster than recursive iterator and filter by regex
            "{$root}/app/code/{community,core,local}/*/*/view/*/*.xml",
            "{$root}/app/design/*/*/*/*/*.xml",
            "{$root}/app/design/*/*/*/*/*/*.xml",
        );
        foreach ($globPatterns as $globPattern) {
            foreach (glob($globPattern, GLOB_BRACE) as $path) {
                $result[] = array($path);
            }
        }
        return $result;
    }

    public static function getMagentoRoot()
    {
        if (!self::$_magentoRoot) {
            self::$_magentoRoot = realpath(__DIR__ . '/../../../../..');
        }
        return self::$_magentoRoot;
    }

    /**
     * Sub-routine to find all unique matches in specified content using specified PCRE
     *
     * @param string $contents
     * @param string $regex
     * @param array &$result
     * @return array
     */
    protected function _collectMatches($contents, $regex, &$result = array())
    {
        preg_match_all($regex, $contents, $matches);
        array_shift($matches);
        foreach ($matches as $row) {
            $result = array_merge($result, $row);
        }
        $result = array_filter(array_unique($result), function($value) {
            return !empty($value);
        });
        return $result;
    }

    /**
     * Check whether specified classes correspond to a file according PSR-0 standard
     *
     * Cyclomatic complexity is because of temporary marking test as incomplete
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $classes
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertClassesExist($classes)
    {
        if (!$classes) {
            return;
        }
        self::getMagentoRoot();
        $badClasses = array();
        foreach ($classes as $class) {
            try {
                $path = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                $this->assertTrue(isset(self::$_existingClasses[$class])
                    || file_exists(self::$_magentoRoot . "/app/code/core/{$path}")
                    || file_exists(self::$_magentoRoot . "/app/code/community/{$path}")
                    || file_exists(self::$_magentoRoot . "/app/code/local/{$path}")
                    || file_exists(self::$_magentoRoot . "/lib/{$path}")
                );
                self::$_existingClasses[$class] = 1;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                if ('Mage_Catalog_Model_Resource_Convert' == $class) {
                    $this->markTestIncomplete('Bug MAGE-4763');
                }
                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail("Missing files with declaration of classes:\n" . implode("\n", $badClasses));
        }
    }
}
