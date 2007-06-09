<?php

class Mage_Core_Model_Resource_Setup
{
    const VERSION_COMPARE_EQUAL  = 0;
    const VERSION_COMPARE_LOWER  = -1;
    const VERSION_COMPARE_GREATER= 1;
    
    protected $_resourceName = null;
    protected $_resourceConfig = null;
    protected $_connectionConfig = null;
    protected $_moduleConfig = null;
    
    public function __construct($resourceName)
    {
        $config = Mage::getConfig();
        $this->_resourceName = $resourceName;
        $this->_resourceConfig = $config->getResourceConfig($resourceName);
        $this->_connectionConfig = $config->getResourceConnectionConfig($resourceName);
        $modName = (string)$this->_resourceConfig->setup->module;
        $this->_moduleConfig = $config->getModuleConfig($modName);
    }

    /**
     * Apply database updates whenever needed
     *
     * @return  boolean
     */
    static public function applyAllUpdates()
    {
        $resources = Mage::getConfig()->getNode('global/resources')->children();
        foreach ($resources as $resource) {
            if (!$resource->setup) {
                continue;
            }
            $className = __CLASS__;
            if (isset($resource->setup->class)) {
                $className = $resource->setup->getClassName();
            }
            $setupClass = new $className($resource->getName());
            $setupClass->applyUpdates();
        }
        return true;
    }
    
    public function applyUpdates()
    {
        $dbVer = Mage::getModel('core_resource/resource')->getDbVersion($this->_resourceName);
        $configVer = (string)$this->_moduleConfig->version;
        // Module is installed
        if ($dbVer!==false) {
             $status = version_compare($configVer, $dbVer);
             switch ($status) {
                case self::VERSION_COMPARE_LOWER:
                    $this->_rollbackResourceDb($configVer, $dbVer);
                    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->_upgradeResourceDb($dbVer, $configVer);
                    break;
                default:
                    return true;
                    break;
             }
        }
        // Module not installed
        elseif ($configVer) {
            $this->_installResourceDb($configVer);
        }
    }

    /**
     * Install resource
     *
     * @param     string $version
     * @return    boll
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _installResourceDb($version)
    {
        $this->_modifyResourceDb('install', '', $version);
        $this->_modifyResourceDb('upgrade', '', $version);
    }

    /**
     * Upgrade DB for new resource version
     *
     * @param string $oldVersion
     * @param string $newVersion
     */
    protected function _upgradeResourceDb($oldVersion, $newVersion)
    {
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
    }

    /**
     * Roll back resource
     *
     * @param     string $newVersion
     * @return    bool
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _rollbackResourceDb($newVersion, $oldVersion)
    {
        $this->_modifyResourceDb('rollback', $newVersion, $oldVersion);
    }

    /**
     * Uninstall resource
     *
     * @param     $version existing resource version
     * @return    bool
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _uninstallResourceDb($version)
    {
        $this->_modifyResourceDb('uninstall', $version, '');
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

    protected function _modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        $resModel = (string)$this->_connectionConfig->model;
        $modName = (string)$this->_moduleConfig[0]->getName();
        
        $sqlFilesDir = Mage::getModuleDir('sql', $modName).DS.$this->_resourceName;
        if (!file_exists($sqlFilesDir)) {
            return false;
        }
        // Read resource files
        $arrAvailableFiles = array();
        $sqlDir = dir($sqlFilesDir);
        while (false !== ($sqlFile = $sqlDir->read())) {
            if (preg_match('#^'.$resModel.'-'.$actionType.'-(.*)\.sql$#i', $sqlFile, $matches)) {
                $arrAvailableFiles[$matches[1]] = $sqlFile;
            }
        }
        $sqlDir->close();
        if (empty($arrAvailableFiles)) {
            return false;
        }
       
        // Get SQL files name 
        $arrModifyFiles = $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrAvailableFiles);
        if (empty($arrModifyFiles)) {
            return false;
        }

        foreach ($arrModifyFiles as $resourceFile) {
            $sqlFile = $sqlFilesDir.DS.$resourceFile['fileName'];
            $sql = file_get_contents($sqlFile);

            // Execute SQL
            if (Mage::registry('resources')->getConnection($this->_resourceName)) {
                try {
                    $result = Mage::registry('resources')->getConnection($this->_resourceName)->multi_query($sql);
                    if ($result) {
                        Mage::getModel('core_resource/resource')->setDbVersion($this->_resourceName, $resourceFile['toVersion']);
                    }
                }
                catch (Exception $e){
                    throw new Exception('SQL error in file:"'.$sqlFile.'" - '.$e->getMessage());
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
                        $arrRes[0] = array('toVersion'=>$version, 'fileName'=>$file);
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
                        $arrRes[] = array('toVersion'=>$infoTo, 'fileName'=>$file);
                    }
                }
                break;
                
            case 'rollback':
                break;
                
            case 'uninstall':
                break;
        }
        return $arrRes;
    }
}