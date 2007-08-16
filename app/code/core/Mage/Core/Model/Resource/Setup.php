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
    protected $_conn = null;
    protected $_tables = array();
    protected $_setupCache = array();

    
    public function __construct($resourceName)
    {
        $config = Mage::getConfig();
        $this->_resourceName = $resourceName;
        $this->_resourceConfig = $config->getResourceConfig($resourceName);
        $this->_connectionConfig = $config->getResourceConnectionConfig($resourceName);
        $modName = (string)$this->_resourceConfig->setup->module;
        $this->_moduleConfig = $config->getModuleConfig($modName);
        $this->_conn = Mage::getSingleton('core/resource')->getConnection($this->_resourceName);
    }
    
    public function setTable($tableName, $realTableName)
    {
        $this->_tables[$tableName] = $realTableName;
        return $this;
    }
    
    public function getTable($tableName) {
        if (!isset($this->_tables[$tableName])) {
            if (Mage::registry('resource')) {
                $this->_tables[$tableName] = Mage::registry('resource')->getTableName($tableName);
            } else {
                $this->_tables[$tableName] = str_replace('/', '_', $tableName);
            }
        }
        return $this->_tables[$tableName];
    }

    /**
     * Apply database updates whenever needed
     *
     * @return  boolean
     */
    static public function applyAllUpdates()
    {
        $resources = Mage::getConfig()->getNode('global/resources')->children();
        foreach ($resources as $resName=>$resource) {
            if (!$resource->setup) {
                continue;
            }
            $className = __CLASS__;
            if (isset($resource->setup->class)) {
                $className = $resource->setup->getClassName();
            }
            $setupClass = new $className($resName);
            $setupClass->applyUpdates();
        }
        return true;
    }
    
    public function applyUpdates()
    {
        $dbVer = Mage::getResourceModel('core/resource')->getDbVersion($this->_resourceName);
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

    protected function _installResourceDb($newVersion)
    {
        $oldVersion = $this->_modifyResourceDb('install', '', $newVersion);
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
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
        if (!is_dir($sqlFilesDir) || !is_readable($sqlFilesDir)) {
            return false;
        }
        // Read resource files
        $arrAvailableFiles = array();
        $sqlDir = dir($sqlFilesDir);
        while (false !== ($sqlFile = $sqlDir->read())) {
            if (preg_match('#^'.$resModel.'-'.$actionType.'-(.*)\.(sql|php)$#i', $sqlFile, $matches)) {
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
            $fileType = pathinfo($resourceFile['fileName'], PATHINFO_EXTENSION);
            $conn = $this->_conn;

            // Execute SQL
            if ($conn) {
                try {
                	switch ($fileType) {
                		case 'sql':
                			$sql = file_get_contents($sqlFile);
                			if ($sql!='') {
                				$result = $conn->multi_query($sql);
                			} else {
                				$result = true;
                			}
                			break;
                			
                		case 'php':
		                    /**
		                     * useful variables: 
		                     * - $conn: setup db connection 
		                     * - $sqlFilesDir: root dir for sql update files
		                     */
                            $result = include($sqlFile);
                			break;
                			
                		default:
                			$result = false;
                	}
                    if ($result) {
                        Mage::getResourceModel('core/resource')->setDbVersion(
                        	$this->_resourceName, $resourceFile['toVersion']);
                    }
                }
                catch (Exception $e){
                    throw Mage::exception('Mage_Core', 'Error in file: "'.$sqlFile.'" - '.$e->getMessage());
                }
            }
            $toVersion = $resourceFile['toVersion'];
        }
        return $toVersion;
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
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = array('toVersion'=>$version, 'fileName'=>$file);
                    }
                }
                break;
                
            case 'upgrade':
                uksort($arrFiles, 'version_compare');
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
    

/******************* UTILITY METHODS *****************/

	/**
	 * Retrieve row or field from table by id or string and parent id
	 *
	 * @param string $table
	 * @param string $idField
	 * @param string|integer $id
	 * @param string $field
	 * @param string $parentField
	 * @param string|integer $parentId
	 * @return mixed|boolean
	 */
    public function getTableRow($table, $idField, $id, $field=null, $parentField=null, $parentId=0)
    {
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }

        if (empty($this->_setupCache[$table][$parentId][$id])) {
            $sql = "select * from $table where $idField=?";
            if (!is_null($parentField)) {
                $sql .= $this->_conn->quoteInto(" and $parentField=?", $parentId);
            }
            $this->_setupCache[$table][$parentId][$id] = $this->_conn->fetchRow($sql, $id);
        }
        if (is_null($field)) {
            return $this->_setupCache[$table][$parentId][$id];
        }
        return isset($this->_setupCache[$table][$parentId][$id][$field]) ? $this->_setupCache[$table][$parentId][$id][$field] : false;
    }

    /**
     * Update one or more fields of table row
     *
     * @param string $table
     * @param string $idField
     * @param string|integer $id
     * @param string|array $field
     * @param mixed|null $value
     * @param string $parentField
     * @param string|integer $parentId
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function updateTableRow($table, $idField, $id, $field, $value=null, $parentField=null, $parentId=0)
    {
        if (is_array($field)) {
            foreach ($field as $f=>$v) {
                $this->updateTableRow($table, $idField, $id, $f, $v, $parentField, $parentId);
            }
            return $this;
        }
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }
        $sql = "update $table set ".$this->_conn->quoteInto("$field=?", $value)." where ".$this->_conn->quoteInto("$idField=?", $id);
        if (!is_null($parentField)) {
            $sql .= $this->_conn->quoteInto(" and $parentField=?", $parentId);
        }
        $this->_conn->query($sql);

        return $this;
    }
    
/******************* CONFIG *****************/
	
	public function addConfigField($path, $label, array $data=array(), $default=null)
	{
		$data['level'] = sizeof(explode('/', $path));
		$data['path'] = $path;
		$data['frontend_label'] = $label;
		if ($id = $this->getTableRow('core/config_field', 'path', $path, 'field_id')) {
			$this->updateTableRow('core/config_field', 'field_id', $id, $data);
		} else {
			if (empty($data['sort_order'])) {
				if ($data['level']===1) {
					$parentWhere = '';
				} else {
					$parentWhere = $this->_conn->quoteInto(" and path like ?", dirname($path).'/%');
				}
				
				$data['sort_order'] = $this->_conn->fetchOne("select max(sort_order) 
					from ".$this->getTable('core/config_field')." 
					where level=?".$parentWhere, $data['level'])+1;
			}
			
			#$this->_conn->raw_query("insert into ".$this->getTable('core/config_field')." (".join(',', array_keys($data)).") values ('".join("','", array_values($data))."')");
			#$this->_conn->insert($this->getTable('core/config_field'), $data);
			Mage::getSingleton('core/resource')->getConnection('core_write')
				->insert($this->getTable('core/config_field'), $data);
		}
		
		if (!is_null($default)) {
			$this->setConfigData($path, $default);
		}
		return $this;
	}
	
	public function setConfigData($path, $value, $scope='default', $scopeId=0, $inherit=0)
	{
		$this->_conn->query("replace into ".$this->getTable('core/config_data')." (scope, scope_id, path, value, inherit) values ('$scope', $scopeId, '$path', '$value', $inherit)");
		return $this;
	}
}