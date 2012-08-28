<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model with methods needed for migration process between Magento versions
 */
class Mage_Core_Model_Resource_Setup_Migration extends Mage_Core_Model_Resource_Setup
{
    const FIELD_CONTENT_TYPE_PLAIN       = 'plain';
    const FIELD_CONTENT_TYPE_XML         = 'xml';
    const FIELD_CONTENT_TYPE_WIKI        = 'wiki';
    const FIELD_CONTENT_TYPE_SERIALIZED  = 'serialized';

    const ENTITY_TYPE_MODEL    = 'Model';
    const ENTITY_TYPE_BLOCK    = 'Block';
    const ENTITY_TYPE_RESOURCE = 'Model_Resource';

    const CONFIG_KEY_PATH_TO_MAP_FILE = 'global/migration/path_to_aliases_map_file';

    protected $_entityTypes = array(self::ENTITY_TYPE_MODEL, self::ENTITY_TYPE_BLOCK, self::ENTITY_TYPE_RESOURCE);

    protected $_rowsPerPage = 1;

    protected $_replaceRules = array();

    protected $_replacements = array();

    protected $_aliasesMap;

    public function appendClassAliasReplace($tableName, $fieldName, $entityType = '',
        $fieldContentType = self::FIELD_CONTENT_TYPE_PLAIN, $additionalWhere = ''
    ) {
        if (!isset($this->_replaceRules[$tableName])) {
            $this->_replaceRules[$tableName] = array();
        }

        if (!isset($this->_replaceRules[$tableName][$fieldName])) {
            $this->_replaceRules[$tableName][$fieldName] = array(
                'entity_type'      => $entityType,
                'content_type'     => $fieldContentType,
                'additional_where' => $additionalWhere
            );
        }
    }

    public function doUpdateClassAliases()
    {
        foreach ($this->_replaceRules as $tableName => $tableRules) {
            $this->_updateClassAliasesInTable($tableName, $tableRules);
        }
    }

    //$tableRules == array([field name] => array('entity type' => [entity type], 'content_type' => [content type]))
    protected function _updateClassAliasesInTable($tableName, array $tableRules)
    {
        foreach ($tableRules as $fieldName => $fieldRule) {
            $pagesCount = ceil(
                $this->_getRowsCount($tableName, $fieldName, $fieldRule['additional_where']) / $this->_rowsPerPage
            );

            if (!isset($this->_replacements[$tableName])) {
                $this->_replacements[$tableName] = array();
            }

            for ($page = 1; $page <= $pagesCount; $page++) {
                $this->_applyFieldRule($tableName, $fieldName, $fieldRule, $page);
            }
        }
    }

    protected function _getRowsCount($tableName, $fieldName, $additionalWhere = '')
    {
        $adapter = $this->getConnection();

        $query = $adapter->select()
            ->from($adapter->getTableName($tableName), array('rows_count' => new Zend_Db_Expr('COUNT(*)')))
            ->where($fieldName . ' IS NOT NULL');

        if (!empty($additionalWhere)) {
            $query->where($additionalWhere);
        }

        return $adapter->fetchOne($query);
    }

    protected function _applyFieldRule($tableName, $fieldName, array $fieldRule, $currPage)
    {
        $tableData = $this->_getTableData($tableName, $fieldName, $fieldRule['additional_where'], $currPage);

        if (!isset($this->_replacements[$tableName][$fieldName])) {
            $this->_replacements[$tableName][$fieldName] = array();
        }

        $fieldReplacements = array();
        foreach ($tableData as $rowData) {
            if (!empty($rowData[$fieldName])) {
                if (!isset($fieldReplacements[$rowData[$fieldName]])
                    && !isset($this->_replacements[$tableName][$fieldName][$rowData[$fieldName]])
                ) {
                    $replacement =
                        $this->_getReplacement(
                            $rowData[$fieldName],
                            $fieldRule['content_type'],
                            $fieldRule['entity_type']
                        );

                    if ($replacement) {
                        $fieldReplacements[$rowData[$fieldName]] = $replacement;
                    }
                }
            }
        }

        $this->_updateRowsData($tableName, $fieldName, $fieldReplacements);

        $this->_replacements[$tableName][$fieldName] =
            array_merge($this->_replacements[$tableName][$fieldName], $fieldReplacements);
    }

    protected function _updateRowsData($tableName, $fieldName, array $fieldReplacements)
    {
        $adapter = $this->getConnection();

        foreach ($fieldReplacements as $from => $to) {
            $adapter->update(
                $adapter->getTableName($tableName),
                array($fieldName => $to),
                array($adapter->quoteIdentifier($fieldName) . ' = ?' => $from)
            );
        }
    }

    protected function _getTableData($tableName, $fieldName, $additionalWhere = '', $currPage = 0)
    {
        $adapter = $this->getConnection();

        $query = $adapter->select()
            ->from($adapter->getTableName($tableName), array($fieldName))
            ->where($fieldName . ' IS NOT NULL');

        if (!empty($additionalWhere)) {
            $query->where($additionalWhere);
        }

        if ($currPage) {
            $query->limitPage($currPage, $this->_rowsPerPage);
        }

        return $adapter->fetchAll($query);
    }

    protected function _getReplacement($data, $contentType, $entityType = '')
    {
        if ($contentType == self::FIELD_CONTENT_TYPE_PLAIN) {
            return $this->_getCorrespondingClassName($data, $entityType);
        }

        // TODO add appropriate behavior for all exist FIELD CONTENT TYPES

        return $data;
    }

    protected function _getCorrespondingClassName($alias, $entityType = '')
    {
        if ($this->_isFactoryName($alias)) {
            if ($className = $this->_getAliasFromMap($alias, $entityType)) {
                return $className;
            }

            list($module, $name) = $this->_getModuleName($alias);

            if (!empty($entityType)) {
                return $this->_getClassName($module, $entityType, $name);
            } else {
                $className = '';
                foreach ($this->_entityTypes as $entityType) {
                    if (empty($className)) {
                        $className = $this->_getClassName($module, $entityType, $name);
                    } else {
                        return '';
                    }
                }
                return $className;
            }
        }

        return '';
    }

    /**
     * Build class name supported in magento 2
     *
     * @param string $module
     * @param string $type
     * @param string $name
     * @return string|bool
     */
    protected function _getClassName($module, $type, $name = null)
    {
        $className = implode('_', array_map('ucfirst', explode('_', $module . '_' . $type . '_' . $name)));

        if (Magento_Autoload::getInstance()->classExists($className)) {
            return $className;
        }

        return '';
    }

    /**
     * Whether the given class name is a factory name
     *
     * @param string $factoryName
     * @return bool
     */
    protected function _isFactoryName($factoryName)
    {
        return false !== strpos($factoryName, '/') || preg_match('/^[a-z\d]+(_[A-Za-z\d]+)?$/', $factoryName);
    }

    /**
     * Transform factory name into a pair of module and name
     *
     * @param string $factoryName
     * @return array
     */
    protected function _getModuleName($factoryName)
    {
        if (false !== strpos($factoryName, '/')) {
            list($module, $name) = explode('/', $factoryName);
        } else {
            $module = $factoryName;
            $name = false;
        }
        if (false === strpos($module, '_')) {
            $module = "Mage_{$module}";
        }
        return array($module, $name);
    }

    protected function _getAliasFromMap($alias, $entityType = '')
    {
        if ($map = $this->_getAliasesMap()) {
            if (!empty($entityType) && isset($map[$entityType]) && !empty($map[$entityType][$alias])) {
                return $map[$entityType][$alias];
            } else {
                $className = '';
                foreach ($this->_entityTypes as $entityType) {
                    if (empty($className)) {
                        if (isset($map[$entityType]) && !empty($map[$entityType][$alias])) {
                            $className = $map[$entityType][$alias];
                        }
                    } else {
                        return '';
                    }
                }
                return $className;
            }
        }

        return '';
    }

    protected function _getAliasesMap()
    {
        if (is_null($this->_aliasesMap)) {
            $this->_aliasesMap = array();

            $map = $this->_loadMap($this->_getPathToMapFile());

            if (!empty($map)) {
                $this->_aliasesMap = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($map);
            }
        }

        return $this->_aliasesMap;
    }

    protected function _loadMap($pathToMapFile)
    {
        $pathToMapFile = Mage::getBaseDir() . DS . $pathToMapFile;
        if (file_exists($pathToMapFile)) {
            return file_get_contents($pathToMapFile);
        }

        return '';
    }

    protected function _getPathToMapFile()
    {
        return Mage::getConfig()->getNode(self::CONFIG_KEY_PATH_TO_MAP_FILE);
    }
}
