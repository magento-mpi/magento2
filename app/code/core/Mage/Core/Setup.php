<?php

class Mage_Core_Setup
{
    const VERSION_COMPARE_EQUAL  = 0;
    const VERSION_COMPARE_LOWER  = -1;
    const VERSION_COMPARE_GREATER= 1;
    
    protected $_module = null;
    
    public function __construct($moduleConfig)
    {
        $this->_module = $moduleConfig;
    }

    /**
     * Apply database updates whenever needed
     *
     * @todo    move somewhere sensible
     * @return  boolean
     */
    public function applyAllDbUpdates()
    {
        $config = Mage::getConfig();
        $modules = $config->getModule()->children();
        foreach ($modules as $module) {
            $setupClass = $config->getModuleSetup($module);
            $setupClass->applyModuleDbUpdates();
        }
        return true;
    }
    
    public function applyModuleDbUpdates()
    {
        $dbVer = Mage::getModel('core', 'Module')->getDbVersion($this->_module->getName());
        $modVer = $this->_module->version;

        // Module is installed
        if ($dbVer!==false) {
             $status = version_compare($modVer, $dbVer);
             switch ($status) {
                case self::VERSION_COMPARE_LOWER:
                    $this->_rollbackDb($modVer, $dbVer);
                    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->_upgradeDb($dbVer, $modVer);
                    break;
                default:
                    return true;
                    break;
             }
        }
        // Module not installed
        elseif ($modVer) {
            $this->_installDb($modVer);
        }
    }

    /**
     * Install module
     *
     * @param     string $moduleVersion
     * @return    boll
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _installDb($moduleVersion)
    {
        $this->_modifySql('install', '', $moduleVersion);
        Mage::getModel('core', 'Module') -> setDbVersion($this->_module->getName(), $moduleVersion);
    }

    /**
     * Upgrade DB for new module version
     *
     * @param string $oldVersion
     * @param string $newVersion
     */
    protected function _upgradeDb($oldVersion, $newVersion)
    {
        $this->_modifySql('upgrade', $oldVersion, $newVersion);
        Mage::getModel('core', 'Module') -> setDbVersion($this->_module->getName(), $newVersion);
    }

    /**
     * Roll back module
     *
     * @param     string $newVersion
     * @return    bool
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _rollbackDb($oldVersion, $newVersion)
    {

    }

    /**
     * Uninstall module
     *
     * @param     $moduleVersion
     * @return    bool
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _uninstallDb($moduleVersion)
    {

    }

    /**
     * Run module modification sql
     *
     * @param     string $actionType install|upgrade|uninstall
     * @param     string $fromVersion
     * @param     string $toVersion
     * @return    bool
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _modifySql($actionType, $fromVersion, $toVersion)
    {
        $arrResources = $this->_getModificationResources($actionType);
        foreach ($arrResources as $resName => $resInfo) {
            
            // Get Resource Object
            $connConfig = Mage::getConfig()->getResourceConnectionConfig($resName);
            if ($connConfig) {
                
                // Get resource type !!! TODO
                $resType = (string)$connConfig->type;

                if ($resType && isset($resInfo[$resType])) {
                    
                    // Get SQL files name 
                    $arrFiles = $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $resInfo[$resType]);
                    foreach ($arrFiles as $fileName) {
                        $sqlFile = Mage::getBaseDir('sql', $this->_module->getName()).DS.$resName.DS.$fileName;
                        $sql = file_get_contents($sqlFile);

                        // Execute SQL
                        Mage::registry('resources')->getConnection($resName)->query($sql);
                    }
                }
            }
        }
    }

    /**
     * Get sql files for modifications
     *
     * @param     $actionType
     * @return    array
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrFiles)
    {
        $arrRes = array();

        switch ($actionType) {
            case 'install':
                ksort($arrFiles);
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = $file;
                    }
                }
                break;
            case 'upgrade':
                ksort($arrFiles);
                foreach ($arrFiles as $version => $file) {
                    $version_info = explode('-', $version);
                    
                    // In array must be 2 elements: 0 => version from, 1 => version to
                    if (count($version_info)!=2) {
                        break;
                    }
                    $infoFrom = $version_info[0];
                    $infoTo   = $version_info[1];
                    if (version_compare($infoFrom, $fromVersion)!==self::VERSION_COMPARE_LOWER
                        && version_compare($infoTo, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[] = $file;
                    }
                }
                break;
        }
        return $arrRes;
    }

    /**
     * Get module resources information for modification
     *
     * @param     string $actionType install|upgrade|uninstall
     * @return    array(
     *              [$resource] => array(
     *                      [$resourceType] => array(
     *                              [$versionInfo] => $fileName
     *                          )
     *                  )
     *            )
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _getModificationResources($actionType)
    {
        $arrSql = array();
        $resourceFilesDir = Mage::getBaseDir('sql', $this->_module->getName());

        if (!file_exists($resourceFilesDir)) {
            return $arrSql;
        }

        $resourceDir = dir($resourceFilesDir);
        while (false !== ($resource = $resourceDir->read())) {
            if ($resource == '.' || $resource == '..' || strstr($resource, '.') !==false ) {
                continue;
            }

            $arrSql[$resource] = array();
            $sqlFilesDir = $resourceFilesDir . DS . $resource;
            
            // RegExp Pattern
            // [resourceType]-[actionType]-[versionInfo].sql
            $filePattern = '/^(.*)-' . $actionType . '-(.*)\.sql$/i';
            
            // Read resource files
            $sqlDir = dir($sqlFilesDir);
            while (false !== ($sqlFile = $sqlDir->read())) {
                if (preg_match($filePattern, $sqlFile, $matches)) {
                    $arrSql[$resource][$matches[1]][$matches[2]] = $sqlFile;
                }
            }
            $sqlDir->close();
        }
        $resourceDir->close();

        return $arrSql;
    }
}