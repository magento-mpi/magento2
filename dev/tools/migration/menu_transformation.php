<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('USAGE', <<<USAGE
$>./menu_transformation.php -- [-a:i:nprdseh]
    additional parameters:
    -a          set area code
    -i          set parent item identifier
    -n          create new menu instructions
    -p          print new menu instructions
    -r          remove menu declaration
    -d          remove menu declaration in dry-run mode
    -s          search for legacy code usage (print file paths)
    -e          output with errors during removing menu declaration
    -h          print usage

    Note:
        1) option -n must be declared with options -i, -a
        2) option -p must be declared with option -i
        3) option -e must be declared with option -r, -d, -p or -n
USAGE
);

$shortOpts = 'a:i:nprdseh';
$options = getopt($shortOpts);

if (isset($options['h'])) {
    print USAGE;
    exit(0);
}

define('LICENSE_FILE_HEADER', <<<LICENSEFILEHEADER
<!--
/**
 * {license_notice}
 *
 * @category    {%CATEGORY_NAME%}
 * @package     {%PACKAGE_NAME%}
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
LICENSEFILEHEADER
);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', realpath(dirname(dirname(dirname(__DIR__)))));

require BP . '/dev/tests/static/framework/bootstrap.php';

$routine = new Routine($options);
if (!$routine->checkRequirements()) {
    print USAGE;
    exit(1);
}

if (!$routine->proceed()) {
    exit(1);
}

exit(0);

class Routine
{
    /**
     * command line arguments
     */
    protected $_areaCode;
    protected $_parentItemID;
    protected $_isCreateMenuActions;
    protected $_isPrintMenuActions;
    protected $_isRemoveMenu;
    protected $_isDryRunMode;
    protected $_isSearchLegacyCode;
    protected $_isOutputWithErrors;

    /**
     * Errors
     * @var array of String
     */
    protected $_errors = array();

    /**
     * @param $options command line arguments
     */
    public function __construct($options)
    {
        $this->_areaCode = isset($options['a'])? $options['a'] : null;
        $this->_parentItemID = isset($options['i'])? $options['i'] : null;

        $this->_isCreateMenuActions = isset($options['n']);
        $this->_isPrintMenuActions = isset($options['p']);

        $this->_isRemoveMenu = isset($options['r']);
        $this->_isDryRunMode = isset($options['d']);

        $this->_isSearchLegacyCode = isset($options['s']);

        $this->_isOutputWithErrors = isset($options['e']);
    }

    /**
     * Check requirements for operations
     *
     * @return bool
     */
    public function checkRequirements()
    {
        if ($this->_isCreateMenuActions && (is_null($this->_areaCode) || is_null($this->_parentItemID))) {
            return false;
        }

        if ($this->_isPrintMenuActions && is_null($this->_parentItemID)) {
            return false;
        }

        return true;
    }

    /**
     * Instructions running method
     *
     * @return bool
     */
    public function proceed()
    {
        if ($this->_isCreateMenuActions) {
            $this->_createMenuInstructions();
        }

        if ($this->_isPrintMenuActions) {
            $this->_printMenuInstructions();
        }

        if ($this->_isRemoveMenu) {
            $this->_removeMenuDeclaration();
        }

        if ($this->_isSearchLegacyCode) {
            $this->_searchLegacyCode(true);
        }

        if ($this->_isOutputWithErrors) {
            $this->_printErrors();
        }

        return empty($this->_errors)? true : false;
    }

    /**
     * Get all configuration files
     *
     * @return array
     */
    protected function _getFiles()
    {
        return array_keys(Utility_Files::init()->getConfigFiles());
    }

    /**
     * Create menu instructions
     *
     * @return bool
     */
    protected function _createMenuInstructions()
    {
        $createdFiles = array();
        foreach ($this->_defineMenuInstructions() as $module=>$menuInstructions) {
            $filePath = $this->_defineFilePath($module);
            $fileHeader = $this->_prepareFileHeader($module);

            $fileContent = $fileHeader
                    . "<config>" . "\n" . "    <menu>" . "\n"
                    . implode("\n", $menuInstructions) . "\n"
                    . "    </menu>" . "\n" . "</config>" . "\n";

            if (file_put_contents($filePath, $fileContent) === false) {
                $this->_addError("Unable to put content into file: {$filePath}");
            } else {
                $createdFiles[] = $filePath;
            }
        }

        if (!empty($createdFiles)) {
            $this->_printFiles($createdFiles);
        }

        return true;
    }

    /**
     * Print menu instructions
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _printMenuInstructions()
    {
        foreach ($this->_defineMenuInstructions() as $defModule=>$menuInstructions) {
            list($pool, $namespace, $module) = explode('_', $defModule);
            echo "Module name: {$namespace}_{$module}\n";
            foreach ($menuInstructions as $instruction) {
                echo "{$instruction}\n";
            }
            echo "\n";
        }
    }

    /**
     * Define file path of new file and make directory with areaCode if not exists
     *
     * @param $module
     * @return string
     */
    protected function _defineFilePath($module)
    {
        $modulePath = str_replace('_', DS, $module);
        $root = BP;
        $dirPath = str_replace(array('/', '\\'), DS, "{$root}/app/code/{$modulePath}/etc/{$this->_areaCode}");
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            mkdir($dirPath);
        }

        return $dirPath . DS . "menu.xml";
    }

    /**
     * Prepare file header with license template
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @param $defModule
     * @return string
     */
    protected function _prepareFileHeader($defModule)
    {
        list($pool, $namespace, $module) = explode('_', $defModule);
        return '<?xml version="1.0"?>' . "\n"
                . str_replace(
                    array('{%CATEGORY_NAME%}', '{%PACKAGE_NAME%}'),
                    array($namespace, $namespace . '_' . $module),
                    LICENSE_FILE_HEADER) . "\n";
    }

    /**
     * Define menu instructions
     *
     * @return array
     */
    protected function _defineMenuInstructions()
    {
        $menuActionsPerModule = array();
        foreach ($this->_parseConfigFiles() as $module => $menuItems) {
            $menuActionsPerModule[$module] = array();
            foreach ($menuItems as $menuItem) {
                $menuActionsPerModule[$module][] = $this->_makeMenuInstruction($menuItem);
            }
        }

        return $menuActionsPerModule;
    }

    /**
     * Create menu instruction from menu item
     *
     * @param $menuItem
     * @return string
     */
    protected function _makeMenuInstruction($menuItem)
    {
        $menuItemInstruction = '';
        if (isset($menuItem->disabled) && $menuItem->disabled == true) {
            $menuItemInstruction .= "        <remove id=\"{$menuItem->id}\" />";
        } else {
            $menuItemInstruction .= '        <add ';
            foreach (array_keys(get_object_vars($menuItem)) as $attributeOfMenuItem) {
                $menuItemInstruction .= $attributeOfMenuItem . '="' . $menuItem->$attributeOfMenuItem . '" ';
            }
            $menuItemInstruction .= "/>";
        }

        return $menuItemInstruction;
    }

    /**
     * Parse all configuration files to define which files should be parsed for a module
     *
     * @return array
     */
    protected function _parseConfigFiles()
    {
        $menuItemsPerModule = array();
        foreach ($this->_searchLegacyCode() as $file) {
            $menuItems = $this->_parseConfigFile($file);
            if (is_null($menuItems)) {
                continue;
            }

            $moduleName = $this->_getModuleNameFromFile($file);
            if (!isset($menuItemsPerModule[$moduleName])) {
                $menuItemsPerModule[$moduleName] = $menuItems;
            } else {
                $menuItemsPerModule[$moduleName] = array_merge($menuItemsPerModule[$moduleName], $menuItems);
            }
        }

        return $menuItemsPerModule;
    }

    /**
     * Parse a configuration file to define menu items
     *
     * @param $file
     * @return array|null
     */
    protected function _parseConfigFile($file)
    {
        $xml = simplexml_load_file($file);
        if ($xml === false) {
            $this->_addError("Unable to load content from file: {$file}\n");
            return null;
        }
        $nodes = $xml->xpath('/config/menu/*') ?: array();
        if (empty($nodes)) {
            return null;
        }

        $menuItems = $this->_parseMenuItems($nodes, $this->_parentItemID, '');
        return empty($menuItems)? null : $menuItems;
    }

    /**
     * Transform xml nodes with menu declaration into array of menu items
     *
     * @param $nodes
     * @param $parentItemID
     * @param string $moduleName
     * @return array
     */
    protected function _parseMenuItems($nodes, $parentItemID, $moduleName = '')
    {
        $itemsResult = array();
        /** @var $node SimpleXmlElement */
        foreach ($nodes as $node) {
            $childNodes = $node->xpath('children/*');

            $item = $this->_parseMenuItem($node, $parentItemID, $moduleName);
            $itemsResult[] = $item;
            if (!empty($childNodes)) {
                $nodeModuleName = isset($item->module)? $item->module : $moduleName;
                $itemsResult = array_merge(
                    $itemsResult,
                    $this->_parseMenuItems($childNodes, $item->id, $nodeModuleName)
                );
            }
        }

        return $itemsResult;
    }

    /**
     * Transform xml node with menu declaration into menu items
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param $node SimpleXmlElement
     * @param $parentItemID string
     * @param $moduleName string
     * @return stdObject
     */
    protected function _parseMenuItem($node, $parentItemID, $moduleName)
    {
        $nodeName = ($parentItemID != $this->_parentItemID)?
                $parentItemID . '_' . $node->getName() : $node->getName();


        $item = (array) $node;
        $nodeModuleName = (isset($item["@attributes"]["module"]))? $item["@attributes"]["module"] : '';
        $itemResult = array();
        if (isset($item['disabled'])) {
            $itemResult = array (
                            'id' => $nodeName,
                            'disabled' => true
                        );
        } else if (isset($item['title'])) {
            $itemResult = array (
                            'id' => $nodeName,
                            'title' => $item['title'],
                            'module' => ($nodeModuleName != '')? $nodeModuleName : $moduleName,
                            'sortOrder' => (isset($item['sort_order']))? $item['sort_order'] : '',
                            'parent' => $parentItemID,
                        );
            if (isset($item['action'])) {
                $itemResult['action'] = $item['action'];
            }
            if (isset($item['resource'])) {
                $itemResult['resource'] = $item['resource'];
            }

            $itemResult = $this->_updateItemDependencies($itemResult, $node);
        }

        return (object) $itemResult;
    }

    /**
     * Update menu item with dependencies if exists
     *
     * @param $item
     * @param $node
     * @return array
     */
    protected function _updateItemDependencies($item, $node)
    {
        $nodeDependencies = $node->xpath('depends');
        if (!empty($nodeDependencies)) {
            foreach ((array) $nodeDependencies[0] as $key => $value) {
                $item['dependsOn' . ucfirst($key)] = $value;
            }
        }
        return $item;
    }

    /**
     * Define pool/namespace/module for a file with menu declaration
     *
     * @param $file
     * @return string
     */
    protected function _getModuleNameFromFile($file)
    {
        $sDir = explode(DS, dirname(dirname(str_replace(array('/', '\\'), DS, $file))));
        $sDirCount = count($sDir);
        return $sDir[$sDirCount-3] . '_' . $sDir[$sDirCount-2] . '_' . $sDir[$sDirCount-1];
    }

    /**
     * Remove menu declaration in files
     *
     * @return array
     */
    protected function _removeMenuDeclaration()
    {
        $updatedFiles = array();
        foreach ($this->_searchLegacyCode() as $file) {
            $fileContent = file_get_contents($file);
            if ($fileContent === false) {
                $this->_addError("Unable to get content from file: {$file}");
                continue;
            }
            $fileNewContent = preg_replace('#[[:space:]]+<menu>.*</menu>(\s+)?\n#ims', "\n", $fileContent);
            if (strcmp($fileContent, $fileNewContent) != 0) {
                $updatedFiles[] = $file;
            }

            if (!$this->_isDryRunMode) {
                if (file_put_contents($file, $fileNewContent) === false) {
                    $this->_addError("Unable to put content into file: {$file}");
                }
            }
        }

        if (!empty($updatedFiles)) {
            echo "Menu declaration is removed in files: \n";
            $this->_printFiles($updatedFiles);
            echo "\n";
        }

        return true;
    }

    /**
     * Search for Legacy Menu Declaration
     *
     * @param bool $isOutput
     * @return array
     */
    protected function _searchLegacyCode($isOutput = false)
    {
        $files = array();
        foreach ($this->_getFiles() as $file) {
            $xml = simplexml_load_file($file);
            if ($xml === false) {
                $this->_addError("Unable to load content from file: {$file}\n");
                continue;
            }
            $nodes = $xml->xpath('/config/menu/*') ?: array();
            if (!empty($nodes)) {
                $files[] = $file;
            }
        }

        if ($isOutput && !empty($files)) {
            echo "Files contains legacy code: \n";
            $this->_printFiles($files);
            echo "\n";
        }

        return $files;
    }

    /**
     * Add error into class error list
     *
     * @param $message
     */
    protected function _addError($message)
    {
        $this->_errors[] = $message;
    }

    /**
     * Print errors
     */
    protected function _printErrors()
    {
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                echo "{$error}\n";
            }
        }
    }

    /**
     * Print list of files with removing root path of directory
     *
     * @param $files
     */
    protected function _printFiles($files)
    {
        foreach ($files as $file) {
            echo str_replace(BP, "", $file) . "\n";
        }
    }
}