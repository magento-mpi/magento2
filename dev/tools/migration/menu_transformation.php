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
    -n          create new menu instructions
    -p          print new menu instructions
    -c          replace usage of active menu item setting according to map
    -m          print map of xpath => menu item identifier
    -r          remove menu declaration
    -d          remove menu declaration or replace active menu item usage in dry-run mode
    -s          search for legacy code usage (print file paths)
    -e          output with errors
    -h          print usage

    Note:
        1) option -n must be declared with options -a
        2) option -e must be declared with option -r, -d, -p or -n
USAGE
);

$shortOpts = 'a:npcmrdseh';
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

/**
 * Routine class for processing with menu transformation
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Routine
{
    /**
     * command line arguments
     */
    protected $_areaCode;
    protected $_parentItemID;
    protected $_isCreateMenuActions;
    protected $_isPrintMenuActions;
    protected $_isReplaceActiveItem;
    protected $_isPrintMenuMap;
    protected $_isRemoveMenu;
    protected $_isDryRunMode;
    protected $_isSearchLegacyCode;
    protected $_isOutputWithErrors;

    /**
     * Map of xpath => menu item id
     * @var array
     */
    protected $_map = array();

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
        $this->_parentItemID = '';

        $this->_isCreateMenuActions = isset($options['n']);
        $this->_isPrintMenuActions = isset($options['p']);

        $this->_isReplaceActiveItem = isset($options['c']);
        $this->_isPrintMenuMap = isset($options['m']);

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
        if ($this->_isCreateMenuActions && is_null($this->_areaCode)) {
            return false;
        }

        return true;
    }

    /**
     * Instructions running method
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
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

        if ($this->_isReplaceActiveItem) {
            $this->_replaceActiveMenuItem();
        }

        if ($this->_isPrintMenuMap) {
            $this->_printMenuMap();
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
     * Get files by type (configuration or php files)
     *
     * @param $type
     * @return array
     */
    protected function _getFiles($type='config')
    {
        switch ($type) {
            case 'config':
                return array_keys(Utility_Files::init()->getConfigFiles());
            case 'php':
                return array_keys(Utility_Files::init()->getPhpFiles(true, false, false));
        }

        return array();
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
     * Print menu items map of xpath to item identifier
     */
    protected function _printMenuMap()
    {
        $this->_parseConfigFiles();
        if (!empty($this->_map)) {
            echo "Defined map of xpath to item_id: \n";
            foreach ($this->_map as $xpath => $id) {
                echo "'{$xpath}' => '{$id}',\n";
            }
            echo "\n";
        }
    }

    /**
     * Replace active menu items by xpath inside map
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _replaceActiveMenuItem()
    {
        $this->_parseConfigFiles();
        if (!empty($this->_map)) {
            foreach ($this->_getFiles('php') as $file) {
                $fileContent = file_get_contents($file);
                if ($fileContent === false) {
                    $this->_addError("Unable to get content from file: {$file}");
                    continue;
                }

                $replacement = array(
                    array(), array()
                );
                foreach ($this->_searchActiveMenuItemUsage($fileContent) as $menuItemXPath => $strForReplacing) {
                    if (isset($this->_map[$menuItemXPath])) {
                        $replacement[0][] = $strForReplacing;
                        $replacement[1][] = str_replace($menuItemXPath, $this->_map[$menuItemXPath], $strForReplacing);
                    }
                }

                $fileNewContent = str_replace($replacement[0], $replacement[1], $fileContent);
                if (strcmp($fileContent, $fileNewContent) != 0) {
                    $updatedFiles[] = $file;
                }

                if (!$this->_isDryRunMode) {
                    if (file_put_contents($file, $fileNewContent) === false) {
                        $this->_addError("Unable to put content into file: {$file}");
                    }
                }
            }
        }

        if (!empty($updatedFiles)) {
            echo "Active menu usage is replaced in files: \n";
            $this->_printFiles($updatedFiles);
            echo "\n";
        }

    }

    /**
     * Search active menu item usage in a file
     *
     * @param $fileContent
     * @return array
     */
    protected function _searchActiveMenuItemUsage($fileContent)
    {
        $menuItemsForReplace = array();
        $matches = array();
        preg_match_all('#->_setActiveMenu\([\'"]([\w\d/_]+)[\'"]\)#Ui', $fileContent, $matches);
        if (!empty($matches[0]) && !empty($matches[1])) {
            foreach ($matches[1] as $index => $menuItemXPath) {
                if (!in_array($menuItemXPath, array_keys($menuItemsForReplace))) {
                    $menuItemsForReplace[$menuItemXPath] = $matches[0][$index];
                }
            }
        }

        return $menuItemsForReplace;
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
     * Define module name from pool/namespace/module
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param $defModule
     * @return string
     */
    protected function _defineModuleName($defModule)
    {
        list($pool, $namespace, $module) = explode('_', $defModule);
        return $namespace . '_' . $module;
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
            $menuItemId = $this->_getRealItemId($menuItem->id);
            $menuItemInstruction .= "        <remove id=\"{$menuItemId}\" />";
        } else {
            $menuItemInstruction .= '        <add ';
            foreach (array_keys(get_object_vars($menuItem)) as $attributeOfMenuItem) {
                if (in_array($attributeOfMenuItem, array('id', 'parent'), true)) {
                    if ($attributeOfMenuItem == 'parent' && $menuItem->$attributeOfMenuItem == '') {
                        continue;
                    }
                    $menuItemId = $this->_getRealItemId($menuItem->$attributeOfMenuItem);
                    $menuItemInstruction .= $attributeOfMenuItem . '="' . $menuItemId . '" ';
                } else {
                    $menuItemInstruction .= $attributeOfMenuItem . '="' . $menuItem->$attributeOfMenuItem . '" ';
                }

            }
            $menuItemInstruction .= "/>";
        }

        return $menuItemInstruction;
    }

    /**
     * Get real menu item identifier according to map
     *
     * @param $menuItemId
     * @return string
     */
    protected function _getRealItemId($menuItemId)
    {
        if ($menuItemId == $this->_parentItemID) {
            return $menuItemId;
        }
        return (isset($this->_map[$menuItemId]))? $this->_map[$menuItemId] : '';
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
            $moduleName = $this->_getModuleNameFromFile($file);
            $menuItems = $this->_parseConfigFile($file, $moduleName);
            if (is_null($menuItems)) {
                continue;
            }

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
     * @param $moduleName
     * @return array|null
     */
    protected function _parseConfigFile($file, $moduleName)
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

        $menuItems = $this->_parseMenuItems($nodes, $this->_parentItemID, $moduleName);
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

            $currentNodeName = '';
            $item = $this->_parseMenuItem($node, $parentItemID, $moduleName);
            if (isset($item->id)) {
                $itemsResult[] = $item;
                $currentNodeName = $item->id;
            } else {
                $currentNodeName = ($parentItemID != $this->_parentItemID)?
                                $parentItemID . '/' . $node->getName() : $node->getName();
            }

            if (!empty($childNodes)) {
                $itemsResult = array_merge(
                    $itemsResult,
                    $this->_parseMenuItems($childNodes, $currentNodeName, $moduleName)
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
                $parentItemID . '/' . $node->getName() : $node->getName();

        $moduleName = $this->_defineModuleName($moduleName);
        $item = (array) $node;
        $itemResult = array();
        if (isset($item['disabled'])) {
            $itemResult = array (
                            'id' => $nodeName,
                            'disabled' => true
                        );
        } else if (isset($item['title'])) {
            $this->_addMenuItemIntoMap($nodeName, $moduleName);
            $nodeModuleName = (isset($item["@attributes"]["module"]))? $item["@attributes"]["module"] : $moduleName;
            $itemResult = array (
                            'id' => $nodeName,
                            'title' => $item['title'],
                            'module' => $nodeModuleName,
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
     * @param $xpath
     * @param $moduleName
     */
    protected function _addMenuItemIntoMap($xpath, $moduleName)
    {
        if (!isset($this->_map[$xpath])) {
            $this->_map[$xpath] = $moduleName . "::" . str_replace('/', '_', $xpath);
        }
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