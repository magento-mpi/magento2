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
        self::skipBuggyFile($file);
        $contents = file_get_contents($file);
        $classes = $this->_collectMatches($contents, '/
            # ::getResourceModel ::getBlockSingleton ::getModel ::getSingleton
            \:\:get(?:ResourceModel | BlockSingleton | Model | Singleton)?\(\s*[\'"]([a-z\d_]+)[\'"]\s*[\),]

            # various methods, first argument
            | \->(?:initReport | addBlock | createBlock | setDataHelperName | getBlockClassName | _?initLayoutMessages
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
            /ix'
        );

        // without modifier "i". Starting from capital letter is a significant characteristic of a class name
        $this->_collectMatches($contents, '/(?:\-> | parent\:\:)(?:_init | setType)\(\s*
                \'([A-Z][a-z\d][A-Za-z\d_]+)\'(?:,\s*\'([A-Z][a-z\d][A-Za-z\d_]+)\')
            \s*\)/x',
            $classes
        );

        $this->_collectResourceHelpersPhp($contents, $classes);

        $this->_assertClassesExist($classes);
    }

    /**
     * Special case: collect resource helper references in PHP-code
     *
     * @param string $contents
     * @param array &$classes
     */
    protected function _collectResourceHelpersPhp($contents, &$classes)
    {
        $matches = $this->_collectMatches($contents, '/(?:\:\:|\->)getResourceHelper\(\s*\'([a-z\d_]+)\'\s*\)/ix');
        foreach ($matches as $moduleName) {
            $classes[] = "{$moduleName}_Model_Resource_Helper_Mysql4";
        }
    }

    /**
     * @return array
     */
    public function phpCodeDataProvider()
    {
        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            PATH_TO_SOURCE_CODE, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
        ));
        $regexIterator = new RegexIterator($recursiveIterator,
            '#(app/(bootstrap|Mage)\.php | app/code/.+\.(php|phtml) | app/design/.+\.phtml | pub/[a-z]+\.php)$#x'
        );
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $result[(string)$fileInfo] = array((string)$fileInfo);
        }
        return $result;
    }

    /**
     * @param string $path
     * @dataProvider configurationDataProvider
     */
    public function testConfiguration($path)
    {
        self::skipBuggyFile($path);
        $xml = simplexml_load_file($path);
        $classes = array();

        // various nodes
        $nodes = $xml->xpath('/config//resource_adapter | //class | //model | //backend_model | //source_model
            | //price_model | //model_token | //writer_model | //clone_model | //frontend_model | //admin_renderer
            | //renderer'
        ) ?: array();
        foreach ($nodes as $node) {
            if (preg_match('/^([A-Z][a-z\d_][A-Za-z\d_]+)\:?/', (string)$node, $matches)) {
                $classes[$matches[1]] = 1;
            }
        }

        // "backend_model" attribute
        $nodes = $xml->xpath('//@backend_model') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            $classes[$node['@attributes']['backend_model']] = 1;
        }

        $this->_collectLoggingExpectedModels($xml, $classes);

        $this->_assertClassesExist(array_keys($classes));
    }

    /**
     * Special case: collect "expected models" from logging xml-file
     *
     * @param SimpleXmlElement $xml
     * @param array &$classes
     */
    protected function _collectLoggingExpectedModels($xml, &$classes)
    {
        $nodes = $xml->xpath('/logging/*/expected_models/* | /logging/*/actions/*/expected_models/*') ?: array();
        foreach ($nodes as $node) {
            $classes[$node->getName()] = 1;
        }
    }

    /**
     * @return array
     */
    public function configurationDataProvider()
    {
        $result = array();
        $excludedFiles = array('wsdl.xml', 'wsdl2.xml', 'wsi.xml');
        $globPattern = PATH_TO_SOURCE_CODE . '/app/code/{community,core,local}/*/*/etc/*.xml';
        foreach (glob($globPattern, GLOB_BRACE) as $path) {
            if (in_array(basename($path), $excludedFiles)) {
                continue;
            }
            $result[$path] = array($path);
        }
        return $result;
    }

    /**
     * @param string $path
     * @dataProvider viewXmlDataProvider
     */
    public function testLayouts($path)
    {
        self::skipBuggyFile($path);
        $xml = simplexml_load_file($path);
        $classes = array();

        // block@type
        $nodes = $xml->xpath('/layout//block[@type]') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            $class = $node['@attributes']['type'];
            $classes[(string)$class] = 1;
        }

        // any text nodes that contain conventional block/model/helper names
        $nodes = $xml->xpath('/layout//action/attributeType | /layout//action[@method="addTab"]/content
            | /layout//action[@method="addRenderer" or @method="addItemRender" or @method="addColumnRender"
                or @method="addPriceBlockType" or @method="addMergeSettingsBlockType"
                or @method="addInformationRenderer" or @method="addOptionRenderer" or @method="addRowItemRender"
                or @method="addDatabaseBlock"]/*[2]
            | /layout//action[@method="setMassactionBlockName" or @method="addProductConfigurationHelper"]/name
            | /layout//action[@method="setEntityModelClass"]/code
            | /layout//*[contains(text(), "_Block_") or contains(text(), "_Model_") or contains(text(), "_Helper_")]'
        ) ?: array();
        foreach ($nodes as $node) {
            $classes[(string)$node] = 1;
        }

        $this->_collectLayoutHelpersAndModules($xml, $classes);

        $this->_assertClassesExist(array_keys($classes));
    }

    /**
     * Special case: collect declaration of helpers and modules in layout files and figure out helper class names
     *
     * @param SimpleXmlElement $xml
     * @param array &$classes
     */
    protected function _collectLayoutHelpersAndModules($xml, &$classes)
    {
        $nodes = $xml->xpath('/layout//@helper | /layout//@module') ?: array();
        foreach ($nodes as $node) {
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
    }

    /**
     * Find XML-files of view layer
     *
     * @return array
     */
    public static function viewXmlDataProvider()
    {
        $result = array();
        $root = PATH_TO_SOURCE_CODE;
        $pool = '{community,core,local}';
        $namespace = $module = $area = $package = $theme = '*';
        $globPatterns = array(
            "{$root}/app/code/{$pool}/{$namespace}/{$module}/view/{$area}/*.xml",
            "{$root}/app/design/{$area}/{$package}/$theme/*.xml",
            // diving 2-3 levels should be enough and that's faster than recursive iterator and filter by regex
            "{$root}/app/code/{$pool}/{$namespace}/{$module}/view/{$area}/*/*.xml",
            "{$root}/app/design/{$area}/{$package}/$theme/*/*.xml",
            "{$root}/app/design/{$area}/{$package}/$theme/*/*/*.xml",
        );
        foreach ($globPatterns as $globPattern) {
            foreach (glob($globPattern, GLOB_BRACE) as $path) {
                $result[$path] = array($path);
            }
        }
        return $result;
    }

    /**
     * Determine that some files must be skipped because implementation, broken by some bug
     *
     * @param string|SplFileInfo $path
     * @return true
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function skipBuggyFile($path)
    {
        $path = (string)$path;
        if (strpos($path, 'app/code/core/Mage/XmlConnect/view/frontend/layout.xml')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Checkout/Pbridge/Result.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Catalog/Product/Price/Giftcard.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Checkout/Payment/Method/List.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Catalog/Product/Options/Giftcard.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/controllers/Paypal/MepController.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Catalog/Product/Related.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/controllers/CartController.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Customer/Storecredit.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Customer/Storecredit.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/controllers/PbridgeController.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/Block/Customer/Address/Form.php')
            || strpos($path, 'app/code/core/Mage/XmlConnect/controllers/CustomerController.php')
        ) {
            self::markTestIncomplete('Bug MMOBAPP-1792');
        }
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
        $badClasses = array();
        $isBug = false;
        foreach ($classes as $class) {
            try {
                if ('Mage_Catalog_Model_Resource_Convert' == $class) {
                    $isBug = true;
                    continue;
                }
                $path = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                $this->assertTrue(isset(self::$_existingClasses[$class])
                    || file_exists(PATH_TO_SOURCE_CODE . "/app/code/core/{$path}")
                    || file_exists(PATH_TO_SOURCE_CODE . "/app/code/community/{$path}")
                    || file_exists(PATH_TO_SOURCE_CODE . "/app/code/local/{$path}")
                    || file_exists(PATH_TO_SOURCE_CODE . "/lib/{$path}")
                );
                self::$_existingClasses[$class] = 1;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail("Missing files with declaration of classes:\n" . implode("\n", $badClasses));
        }
        if ($isBug) {
            $this->markTestIncomplete('Bug MAGE-4763');
        }
    }
}
