<?php

abstract class Mage_Core_Setup_Abstract
{
    const VERSION_COMPARE_EQUAL  = 0;
    const VERSION_COMPARE_LOWER  = -1;
    const VERSION_COMPARE_GREATER= 1;
    
    /**
     * Module information
     *
     * [version, name]
     *
     * @var array
     */
    protected $_info = null;

    protected $_moduleInfo = null;
    
    public function __construct($modInfo)
    {
        $this->_moduleInfo = $modInfo;
    }

    public function getInfo($key='')
    {
        if (''===$key) {
            return $this->_info;
        } else {
            return $this->_info[$key];
        }
    }
    
    public function getModuleInfo()
    {
        return $this->_moduleInfo;
    }

    public function applyDbUpdates()
    {
        $dbVer = $this->_moduleInfo->getDbVersion();
        $modVer = $this->_moduleInfo->getCodeVersion();

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
     * @return	  boll
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _installDb($moduleVersion)
    {
        $this->_modifySql('install', '', $moduleVersion);
        Mage::getModel('core', 'Module') -> setDbVersion($this->_moduleInfo->getName(), $moduleVersion);
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
    	Mage::getModel('core', 'Module') -> setDbVersion($this->_moduleInfo->getName(), $newVersion);
    }

    /**
     * Roll back module
     *
     * @param     string $newVersion
     * @return	  bool
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _rollbackDb($oldVersion, $newVersion)
    {

    }

    /**
     * Uninstall module
     *
     * @param     $moduleVersion
     * @return	  bool
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
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
     * @return	  bool
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _modifySql($actionType, $fromVersion, $toVersion)
    {
        $arrResources = $this->_getModificationResources($actionType);
    	foreach ($arrResources as $resName => $resInfo) {
    	    
    	    // Get Resource Object
    	    $resource = Mage_Core_Resource::getResource($resName);
    	    if ($resource) {
    	        
    	        // Get resource type
        	    $resType = $resource->getConfig('type');
                if ($resType && isset($resInfo[$resType])) {
                    
                    // Get SQL files name 
                    $arrFiles = $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $resInfo[$resType]);
                    foreach ($arrFiles as $fileName) {
                    	$sqlFile = $this->_moduleInfo->getRoot('sql').DS.$resName.DS.$fileName;
                    	$sql = file_get_contents($sqlFile);

                    	// Execute SQL
                    	$resource->getConnection()->query($sql);
                    }
                }
    	    }
    	}
    }

    /**
     * Get sql files for modifications
     *
     * @param     $actionType
     * @return	  array
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
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
     * @return	  array(
     *              [$resource] => array(
     *                      [$resourceType] => array(
     *                              [$versionInfo] => $fileName
     *                          )
     *                  )
     *            )
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */

    protected function _getModificationResources($actionType)
    {
        $arrSql = array();
    	$resourceFilesDir = $this->_moduleInfo->getRoot('sql');

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