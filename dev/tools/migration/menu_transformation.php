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
    protected $_isRemoveDeclaredMenu;
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

        $this->_isRemoveDeclaredMenu = isset($options['r']);
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

        if ($this->_isRemoveDeclaredMenu) {
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
     * Create menu instruction
     */
    protected function _createMenuInstructions()
    {
        foreach ($this->_defineMenuInstructions() as $module=>$menuInstructions) {
            $filePath = $this->_defineFilePath($module);
            $fileHeader = $this->_prepareFileHeader($module);

            $fileContent = $fileHeader
                    . "<config>" . "\n" . "    <menu>" . "\n"
                    . implode("\n", $menuInstructions) . "\n"
                    . "    </menu>" . "\n" . "</config>" . "\n";

            if (file_put_contents($filePath, $fileContent) === false) {
                $this->_addError("Unable to put content into file: {$filePath}");
            }
        }
    }


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

    protected function _defineFilePath($module)
    {
        $modulePath = str_replace('_', DS, $module);
        $root = BP;
        $dirPath = str_replace(array('/', '\\'), DS,"{$root}/app/code/{$modulePath}/etc/{$this->_areaCode}");
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            mkdir($dirPath);
        }

        return $dirPath . DS . "menu.xml";
    }

    protected function _prepareFileHeader($defModule)
    {
        list($pool, $namespace, $module) = explode('_', $defModule);
        return '<?xml version="1.0"?>' . "\n"
                . str_replace(
                    array('{%CATEGORY_NAME%}', '{%PACKAGE_NAME%}'),
                    array($namespace, $namespace . '_' . $module),
                    LICENSE_FILE_HEADER) . "\n";
    }

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
            $nodeName = ($parentItemID != $this->_parentItemID)?
                    $parentItemID . '_' . $node->getName() : $node->getName();
            $nodeDependencies = $node->xpath('depends');
            $itemDependencies = array();
            if (!empty($nodeDependencies)) {
                $itemDependencies = (array) $nodeDependencies[0];
            }

            $childNodes = $node->xpath('children/*');

            $node = (array) $node;
            $nodeModuleName = (isset($node["@attributes"]["module"]))? $node["@attributes"]["module"] : '';

            if (isset($node['disabled'])) {
                $itemResult = array (
                                'id' => $nodeName,
                                'disabled' => true
                            );
                $itemsResult[] = (object) $itemResult;
            }

            if (!isset($node['title'])) {
                if (!empty($childNodes)) {
                    $itemsResult = array_merge(
                        $itemsResult,
                        $this->_parseMenuItems($childNodes, $nodeName, $nodeModuleName)
                    );
                }
                continue;
            } else {
                $itemResult = array (
                                'id' => $nodeName,
                                'title' => (isset($node['title']))? $node['title'] : '',
                                'module' => ($nodeModuleName != '')? $nodeModuleName : $moduleName,
                                'sortOrder' => (isset($node['sort_order']))? $node['sort_order'] : '',
                                'parent' => $parentItemID,
                            );
                if (isset($node['action'])) {
                    $itemResult['action'] = $node['action'];
                }
                if (isset($node['resource'])) {
                    $itemResult['resource'] = $node['resource'];
                }
                if (!empty($itemDependencies)) {
                    foreach ($itemDependencies as $key => $value) {
                        $itemResult['dependsOn' . ucfirst($key)] = $value;
                    }
                }
                $itemsResult[] = (object) $itemResult;
                if (!empty($childNodes)) {
                    $itemsResult = array_merge(
                        $itemsResult,
                        $this->_parseMenuItems($childNodes, $nodeName, $nodeModuleName)
                    );
                }
            }
        }

        return $itemsResult;
    }

    /**
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

        return $updatedFiles;
    }

    /**
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
            foreach ($files as $file) {
                echo str_replace(BP, "", $file) . "\n";
            }
            echo "\n";
        }

        return $files;
    }

    /**
     * @param $message
     */
    protected function _addError($message)
    {
        $this->_errors[] = $message;
    }

    /**
     *
     */
    protected function _printErrors()
    {
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                echo "{$error}\n";
            }
        }
    }
}
