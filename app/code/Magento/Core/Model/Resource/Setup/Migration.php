<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Setup;

use Magento\Filesystem\DirectoryList;

/**
 * Resource setup model with methods needed for migration process between Magento versions
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Migration extends \Magento\Core\Model\Resource\Setup
{
    /**#@+
     * Type of field content where class alias is used
     */
    const FIELD_CONTENT_TYPE_PLAIN       = 'plain';
    const FIELD_CONTENT_TYPE_XML         = 'xml';
    const FIELD_CONTENT_TYPE_WIKI        = 'wiki';
    const FIELD_CONTENT_TYPE_SERIALIZED  = 'serialized';
    /**#@-*/

    /**#@+
     *  Entity type of alias
     */
    const ENTITY_TYPE_MODEL    = 'Model';
    const ENTITY_TYPE_BLOCK    = 'Block';
    const ENTITY_TYPE_RESOURCE = 'Model_Resource';
    /**#@-*/

    /**#@+
     *  Find/replace patterns
     */
    const PLAIN_FIND_PATTERN         = '/^(?P<alias>[a-z]+[_a-z\d]*?\/[a-z]+[_a-z\d]*?)::.*?$/sui';
    const WIKI_FIND_PATTERN
        = '/{{(block|widget).*?(class|type)=\"(?P<alias>[a-z]+[_a-z\d]*?\/[a-z]+[_a-z\d]*?)\".*?}}/sui';
    const XML_FIND_PATTERN           = '/<block.*?class=\"(?P<alias>[a-z]+[_a-z\d]*?\/[a-z]+[_a-z\d]*?)\".*?>/sui';
    const SERIALIZED_FIND_PATTERN    = '#(?P<string>s:\d+:"(?P<alias>[a-z]+[_a-z\d]*?/[a-z]+[_a-z\d]*?)")#sui';
    const SERIALIZED_REPLACE_PATTERN = 's:%d:"%s"';
    /**#@-*/

    /**
     * Config key for path to aliases map file
     *
     * @var string
     */
    protected $_confPathToMapFile;

    /**
     * List of possible entity types sorted by possibility of usage
     *
     * @var array
     */
    protected $_entityTypes = array(self::ENTITY_TYPE_MODEL, self::ENTITY_TYPE_BLOCK, self::ENTITY_TYPE_RESOURCE);

    /**
     * Rows per page. To split processing data from tables
     *
     * @var int
     */
    protected $_rowsPerPage = 100;

    /**
     * Replace rules for tables
     *
     * [table name] => array(
     *     [field name] => array(
     *         'entity_type'      => [entity type]
     *         'content_type'     => [content type]
     *         'additional_where' => [additional where]
     *     )
     * )
     *
     * @var array
     */
    protected $_replaceRules = array();

    /**
     * Aliases to classes map
     *
     * [entity type] => array(
     *     [alias] => [class name]
     * )
     *
     * @var array
     */
    protected $_aliasesMap;

    /**
     * Replacement regexps for specified content types
     *
     * @var array
     */
    protected $_replacePatterns = array(
        self::FIELD_CONTENT_TYPE_WIKI => self::WIKI_FIND_PATTERN,
        self::FIELD_CONTENT_TYPE_XML  => self::XML_FIND_PATTERN,
    );

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * Path to map file from config
     *
     * @var string
     */
    protected $_pathToMapFile;

    /**
     * List of composite module names
     *
     * @var array
     */
    protected $_compositeModules;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $_directory;

    /**
     * @param Context $context
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Helper\Data $helper
     * @param $resourceName
     * @param $confPathToMapFile
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Helper\Data $helper,
        $resourceName,
        $confPathToMapFile,
        $moduleName = 'Magento_Core',
        $connectionName = ''
    ) {
        $this->_directory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->_coreHelper = $helper;
        $this->_pathToMapFile = $confPathToMapFile;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * Add alias replace rule
     *
     * @param string $tableName name of table to replace aliases in
     * @param string $fieldName name of table column to replace aliases in
     * @param string $entityType entity type of alias
     * @param string $fieldContentType type of field content where class alias is used
     * @param array $primaryKeyFields row pk field(s) to update by
     * @param string $additionalWhere additional where condition
     * @return void
     */
    public function appendClassAliasReplace($tableName, $fieldName, $entityType = '',
        $fieldContentType = self::FIELD_CONTENT_TYPE_PLAIN, array $primaryKeyFields = array(), $additionalWhere = ''
    ) {
        if (!isset($this->_replaceRules[$tableName])) {
            $this->_replaceRules[$tableName] = array();
        }

        if (!isset($this->_replaceRules[$tableName][$fieldName])) {
            $this->_replaceRules[$tableName][$fieldName] = array(
                'entity_type'      => $entityType,
                'content_type'     => $fieldContentType,
                'pk_fields'        => $primaryKeyFields,
                'additional_where' => $additionalWhere,
            );
        }
    }

    /**
     * Start process of replacing aliases with class names using rules
     */
    public function doUpdateClassAliases()
    {
        foreach ($this->_replaceRules as $tableName => $tableRules) {
            $this->_updateClassAliasesInTable($tableName, $tableRules);
        }
    }

    /**
     * Update class aliases in table
     *
     * @param string $tableName name of table to replace aliases in
     * @param array $tableRules replacing rules for table
     */
    protected function _updateClassAliasesInTable($tableName, array $tableRules)
    {
        foreach ($tableRules as $fieldName => $fieldRule) {
            $pagesCount = ceil(
                $this->_getRowsCount($tableName, $fieldName, $fieldRule['additional_where']) / $this->_rowsPerPage
            );

            for ($page = 1; $page <= $pagesCount; $page++) {
                $this->_applyFieldRule($tableName, $fieldName, $fieldRule, $page);
            }
        }
    }

    /**
     * Get amount of rows for table column which should be processed
     *
     * @param string $tableName name of table to replace aliases in
     * @param string $fieldName name of table column to replace aliases in
     * @param string $additionalWhere additional where condition
     *
     * @return int
     */
    protected function _getRowsCount($tableName, $fieldName, $additionalWhere = '')
    {
        $adapter = $this->getConnection();

        $query = $adapter->select()
            ->from($this->getTable($tableName), array('rows_count' => new \Zend_Db_Expr('COUNT(*)')))
            ->where($fieldName . ' IS NOT NULL');

        if (!empty($additionalWhere)) {
            $query->where($additionalWhere);
        }

        return (int) $adapter->fetchOne($query);
    }

    /**
     * Replace aliases with class names in rows
     *
     * @param string $tableName name of table to replace aliases in
     * @param string $fieldName name of table column to replace aliases in
     * @param array $fieldRule
     * @param int $currentPage
     */
    protected function _applyFieldRule($tableName, $fieldName, array $fieldRule, $currentPage = 0)
    {
        $fieldsToSelect = array($fieldName);
        if (!empty($fieldRule['pk_fields'])) {
            $fieldsToSelect = array_merge($fieldsToSelect, $fieldRule['pk_fields']);
        }
        $tableData = $this->_getTableData($tableName, $fieldName, $fieldsToSelect, $fieldRule['additional_where'],
            $currentPage
        );

        $fieldReplacements = array();
        foreach ($tableData as $rowData) {
            $replacement = $this->_getReplacement($rowData[$fieldName], $fieldRule['content_type'],
                $fieldRule['entity_type']
            );
            if ($replacement !== $rowData[$fieldName]) {
                $fieldReplacement = array(
                    'to' => $replacement
                );
                if (empty($fieldRule['pk_fields'])) {
                    $fieldReplacement['where_fields'] = array($fieldName => $rowData[$fieldName]);
                } else {
                    $fieldReplacement['where_fields'] = array();
                    foreach ($fieldRule['pk_fields'] as $pkField) {
                        $fieldReplacement['where_fields'][$pkField] = $rowData[$pkField];
                    }
                }
                $fieldReplacements[] = $fieldReplacement;
            }
        }

        $this->_updateRowsData($tableName, $fieldName, $fieldReplacements);
    }

    /**
     * Update rows data in database
     *
     * @param string $tableName
     * @param string $fieldName
     * @param array $fieldReplacements
     */
    protected function _updateRowsData($tableName, $fieldName, array $fieldReplacements)
    {
        if (count($fieldReplacements) > 0) {
            $adapter = $this->getConnection();

            foreach ($fieldReplacements as $fieldReplacement) {
                $where = array();
                foreach ($fieldReplacement['where_fields'] as $whereFieldName => $value) {
                    $where[$adapter->quoteIdentifier($whereFieldName) . ' = ?'] = $value;
                }
                $adapter->update(
                    $this->getTable($tableName),
                    array($fieldName => $fieldReplacement['to']),
                    $where
                );
            }
        }
    }

    /**
     * Get data for table column which should be processed
     *
     * @param string $tableName name of table to replace aliases in
     * @param string $fieldName name of table column to replace aliases in
     * @param array $fieldsToSelect array of fields to select
     * @param string $additionalWhere additional where condition
     * @param int $currPage
     *
     * @return array
     */
    protected function _getTableData($tableName, $fieldName, array $fieldsToSelect, $additionalWhere = '',
        $currPage = 0
    ) {
        $adapter = $this->getConnection();

        $query = $adapter->select()
            ->from($this->getTable($tableName), $fieldsToSelect)
            ->where($fieldName . ' IS NOT NULL');

        if (!empty($additionalWhere)) {
            $query->where($additionalWhere);
        }

        if ($currPage) {
            $query->limitPage($currPage, $this->_rowsPerPage);
        }

        return $adapter->fetchAll($query);
    }

    /**
     * Get data with replaced aliases with class names
     *
     * @param string $data
     * @param string $contentType type of data (field content)
     * @param string $entityType entity type of alias
     *
     * @return string
     */
    protected function _getReplacement($data, $contentType, $entityType = '')
    {
        switch ($contentType) {
            case self::FIELD_CONTENT_TYPE_SERIALIZED:
                $data = $this->_getAliasInSerializedStringReplacement($data, $entityType);
                break;
            // wiki and xml content types use the same replacement method
            case self::FIELD_CONTENT_TYPE_WIKI:
            case self::FIELD_CONTENT_TYPE_XML:
                $data = $this->_getPatternReplacement($data, $contentType, $entityType);
                break;
            case self::FIELD_CONTENT_TYPE_PLAIN:
            default:
                $data = $this->_getModelReplacement($data, $entityType);
                break;
        }

        return $data;
    }

    /**
     * Get appropriate class name for alias
     *
     * @param string $alias
     * @param string $entityType entity type of alias
     *
     * @return string
     */
    protected function _getCorrespondingClassName($alias, $entityType = '')
    {
        if ($this->_isFactoryName($alias)) {
            if ($className = $this->_getAliasFromMap($alias, $entityType)) {
                return $className;
            }

            list($module, $name) = $this->_getModuleName($alias);

            if (!empty($entityType)) {
                $className = $this->_getClassName($module, $entityType, $name);
                $properEntityType = $entityType;
            } else {
                // Try to find appropriate class name for all entity types
                $className = '';
                $properEntityType = '';
                foreach ($this->_entityTypes as $entityType) {
                    if (empty($className)) {
                        $className = $this->_getClassName($module, $entityType, $name);
                        $properEntityType = $entityType;
                    } else {
                        // If was found more than one match - alias cannot be replaced
                        return '';
                    }
                }
            }
            $this->_pushToMap($properEntityType, $alias, $className);
            return $className;
        }

        return '';
    }

    /**
     * Replacement for model alias and model alias with method
     *
     * @param string $data
     * @param string $entityType
     * @return string
     */
    protected function _getModelReplacement($data, $entityType = '')
    {
        if (preg_match(self::PLAIN_FIND_PATTERN, $data, $matches)) {
            $classAlias = $matches['alias'];
            $className = $this->_getCorrespondingClassName($classAlias, $entityType);
            if ($className) {
                return str_replace($classAlias, $className, $data);
            }
        }

        $className = $this->_getCorrespondingClassName($data, $entityType);
        if (!empty($className)) {
            return $className;
        } else {
            return $data;
        }
    }

    /**
     * Replaces class aliases using pattern
     *
     * @param string $data
     * @param string $contentType
     * @param string $entityType
     * @return string|null
     */
    protected function _getPatternReplacement($data, $contentType, $entityType = '')
    {
        if (!array_key_exists($contentType, $this->_replacePatterns)) {
            return null;
        }

        $replacements = array();
        $pattern      = $this->_replacePatterns[$contentType];
        preg_match_all($pattern, $data, $matches, PREG_PATTERN_ORDER);
        if (isset($matches['alias'])) {
            $matches = array_unique($matches['alias']);
            foreach ($matches as $classAlias) {
                $className = $this->_getCorrespondingClassName($classAlias, $entityType);
                if ($className) {
                    $replacements[$classAlias] = $className;
                }
            }
        }

        foreach ($replacements as $classAlias => $className) {
            $data = str_replace($classAlias, $className, $data);
        }

        return $data;
    }

    /**
     * Generate class name
     *
     * @param string $module
     * @param string $type
     * @param string $name
     *
     * @return string
     */
    protected function _getClassName($module, $type, $name = null)
    {
        $className = implode('\\', array_map('ucfirst', explode('_', $module . '_' . $type . '_' . $name)));

        if (class_exists($className)) {
            return $className;
        }

        return '';
    }

    /**
     * Whether the given class name is a factory name
     *
     * @param string $factoryName
     *
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
     *
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
        $compositeModuleName = $this->_getCompositeModuleName($module);
        if (null !== $compositeModuleName) {
            $module = $compositeModuleName;
        } elseif (false === strpos($module, '_')) {
            $module = "Magento_{$module}";
        }
        return array($module, $name);
    }

    /**
     * Get composite module name by module alias
     *
     * @param $moduleAlias
     *
     * @return string|null
     */
    protected function _getCompositeModuleName($moduleAlias)
    {
        if (null === $this->_compositeModules) {
            $this->_compositeModules = static::getCompositeModules();
        }
        if (array_key_exists($moduleAlias, $this->_compositeModules)) {
            return $this->_compositeModules[$moduleAlias];
        }
        return null;
    }

    /**
     * Search class by alias in map
     *
     * @param string $alias
     * @param string $entityType
     *
     * @return string
     */
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

    /**
     * Store already generated class name for alias
     *
     * @param $entityType
     * @param $alias
     * @param $className
     */
    protected function _pushToMap($entityType, $alias, $className)
    {
        // Load map from file if it wasn't loaded
        $this->_getAliasesMap();

        if (!isset($this->_aliasesMap[$entityType])) {
            $this->_aliasesMap[$entityType] = array();
        }

        if (!isset($this->_aliasesMap[$entityType][$alias])) {
            $this->_aliasesMap[$entityType][$alias] = $className;
        }
    }

    /**
     * Retrieve aliases to classes map if exit
     *
     * @return array
     */
    protected function _getAliasesMap()
    {
        if (is_null($this->_aliasesMap)) {
            $this->_aliasesMap = array();

            $map = $this->_loadMap($this->_pathToMapFile);

            if (!empty($map)) {
                $this->_aliasesMap = $this->_coreHelper->jsonDecode($map);
            }
        }

        return $this->_aliasesMap;
    }

    /**
     * Load aliases to classes map from file
     *
     * @param string $pathToMapFile
     *
     * @return string
     */
    protected function _loadMap($pathToMapFile)
    {
        if ($this->_directory->isFile($pathToMapFile)) {
            return $this->_directory->readFile($pathToMapFile);
        }

        return '';
    }

    /**
     * @param string $data
     * @param string $entityType
     * @return mixed
     */
    protected function _getAliasInSerializedStringReplacement($data, $entityType = '')
    {
        $matches = $this->_parseSerializedString($data);
        if (isset($matches['alias']) && count($matches['alias']) > 0) {
            foreach ($matches['alias'] as $key => $alias) {
                $className = $this->_getCorrespondingClassName($alias, $entityType);

                if (!empty($className)) {
                    $replaceString = sprintf(self::SERIALIZED_REPLACE_PATTERN, strlen($className), $className);
                    $data = str_replace($matches['string'][$key], $replaceString, $data);
                }
            }
        }

        return $data;
    }

    /**
     * Parse class aliases from serialized string
     *
     * @param $string
     * @return array
     */
    protected function _parseSerializedString($string)
    {
        if ($string && preg_match_all(self::SERIALIZED_FIND_PATTERN, $string, $matches)) {
            unset($matches[0], $matches[1], $matches[2]);
            return $matches;
        } else {
            return array();
        }
    }

    /**
     * List of correspondence between composite module aliases and module names
     *
     * @static
     * @return array
     */
    public static function getCompositeModules()
    {
        return array(
            'adminnotification' => 'Magento_AdminNotification',
            'catalogindex'      => 'Magento_CatalogIndex',
            'cataloginventory'  => 'Magento_CatalogInventory',
            'catalogrule'       => 'Magento_CatalogRule',
            'catalogsearch'     => 'Magento_CatalogSearch',
            'currencysymbol'    => 'Magento_CurrencySymbol',
            'giftmessage'       => 'Magento_GiftMessage',
            'googleanalytics'   => 'Magento_GoogleAnalytics',
            'googlebase'        => 'Magento_GoogleBase',
            'googlecheckout'    => 'Magento_GoogleCheckout',
            'importexport'      => 'Magento_ImportExport',
            'paypaluk'          => 'Magento_PaypalUk',
            'productalert'      => 'Magento_ProductAlert',
            'salesrule'         => 'Magento_SalesRule',
        );
    }
}
