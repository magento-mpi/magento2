<?php
/**
 * Abstract DB helper class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DB\Helper;

abstract class AbstractHelper
{
    /**
     * Read adapter instance
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_readAdapter;

    /**
     * Write adapter instance
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_writeAdapter;

    /**
     * Resource helper module prefix
     *
     * @var string
     */
    protected $_modulePrefix;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_resource;

    /**
     * Initialize resource helper instance
     *
     * @param \Magento\Framework\App\Resource $resource
     * @param string $modulePrefix
     */
    public function __construct(\Magento\Framework\App\Resource $resource, $modulePrefix)
    {
        $this->_resource = $resource;
        $this->_modulePrefix = (string)$modulePrefix;
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function _getReadAdapter()
    {
        if (null === $this->_readAdapter) {
            $this->_readAdapter = $this->_getConnection('read');
        }

        return $this->_readAdapter;
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function _getWriteAdapter()
    {
        if (null === $this->_writeAdapter) {
            $this->_writeAdapter = $this->_getConnection('write');
        }

        return $this->_writeAdapter;
    }

    /**
     * Retrieves connection to the resource
     *
     * @param string $name
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function _getConnection($name)
    {
        $connection = sprintf('%s_%s', $this->_modulePrefix, $name);

        return $this->_resource->getConnection($connection);
    }

    /**
     * Escapes value, that participates in LIKE, with '\' symbol.
     * Note: this func cannot be used on its own, because different RDMBS may use different default escape symbols,
     * so you should either use addLikeEscape() to produce LIKE construction, or add escape symbol on your own.
     *
     * By default escapes '_', '%' and '\' symbols. If some masking symbols must not be escaped, then you can set
     * appropriate options in $options.
     *
     * $options can contain following flags:
     * - 'allow_symbol_mask' - the '_' symbol will not be escaped
     * - 'allow_string_mask' - the '%' symbol will not be escaped
     * - 'position' ('any', 'start', 'end') - expression will be formed so that $value will be found at position
     *      within string, by default when nothing set - string must be fully matched with $value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function escapeLikeValue($value, $options = array())
    {
        $value = str_replace('\\', '\\\\', $value);

        $replaceFrom = array();
        $replaceTo = array();
        if (empty($options['allow_symbol_mask'])) {
            $replaceFrom[] = '_';
            $replaceTo[] = '\_';
        }
        if (empty($options['allow_string_mask'])) {
            $replaceFrom[] = '%';
            $replaceTo[] = '\%';
        }
        if ($replaceFrom) {
            $value = str_replace($replaceFrom, $replaceTo, $value);
        }

        if (isset($options['position'])) {
            switch ($options['position']) {
                case 'any':
                    $value = '%' . $value . '%';
                    break;
                case 'start':
                    $value = $value . '%';
                    break;
                case 'end':
                    $value = '%' . $value;
                    break;
                default:
                    break;
            }
        }

        return $value;
    }

    /**
     * Escapes, quotes and adds escape symbol to LIKE expression.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $value
     * @param array $options
     * @return \Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    abstract public function addLikeEscape($value, $options = array());

    /**
     * Returns case insensitive LIKE construction.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $field
     * @param string $value
     * @param array $options
     * @return \Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function getCILike($field, $value, $options = array())
    {
        $quotedField = $this->_getReadAdapter()->quoteIdentifier($field);
        return new \Zend_Db_Expr($quotedField . ' LIKE ' . $this->addLikeEscape($value, $options));
    }

    /**
     * Converts old pre-MMDB column definition for MySQL to new cross-db column DDL definition.
     * Used to convert data from 3rd party extensions that hasn't been updated to MMDB style yet.
     *
     * E.g. Converts type 'varchar(255)' to array('type' => \Magento\DB\Ddl\Table::TYPE_TEXT, 'length' => 255)
     *
     * @param array $column
     * @return array
     * @throws \Magento\Model\Exception
     */
    public function convertOldColumnDefinition($column)
    {
        // Match type and size - e.g. varchar(100) or decimal(12,4) or int
        $matches = array();
        $definition = trim($column['type']);
        if (!preg_match('/([^(]*)(\\((.*)\\))?/', $definition, $matches)) {
            throw new \Magento\Model\Exception(__("Wrong old style column type definition: {$definition}."));
        }

        $length = null;
        $proposedLength = isset($matches[3]) && strlen($matches[3]) ? $matches[3] : null;
        switch (strtolower($matches[1])) {
            case 'bool':
                $length = null;
                $type = \Magento\DB\Ddl\Table::TYPE_BOOLEAN;
                break;
            case 'char':
            case 'varchar':
            case 'tinytext':
                $length = $proposedLength;
                if (!$length) {
                    $length = 255;
                }
                $type = \Magento\DB\Ddl\Table::TYPE_TEXT;
                break;
            case 'text':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_TEXT;
                break;
            case 'mediumtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_TEXT;
                break;
            case 'longtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_TEXT;
                break;
            case 'blob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_BLOB;
                break;
            case 'mediumblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_BLOB;
                break;
            case 'longblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = \Magento\DB\Ddl\Table::TYPE_BLOB;
                break;
            case 'tinyint':
            case 'smallint':
                $type = \Magento\DB\Ddl\Table::TYPE_SMALLINT;
                break;
            case 'mediumint':
            case 'int':
                $type = \Magento\DB\Ddl\Table::TYPE_INTEGER;
                break;
            case 'bigint':
                $type = \Magento\DB\Ddl\Table::TYPE_BIGINT;
                break;
            case 'float':
                $type = \Magento\DB\Ddl\Table::TYPE_FLOAT;
                break;
            case 'decimal':
            case 'numeric':
                $length = $proposedLength;
                $type = \Magento\DB\Ddl\Table::TYPE_DECIMAL;
                break;
            case 'datetime':
                $type = \Magento\DB\Ddl\Table::TYPE_DATETIME;
                break;
            case 'timestamp':
            case 'time':
                $type = \Magento\DB\Ddl\Table::TYPE_TIMESTAMP;
                break;
            case 'date':
                $type = \Magento\DB\Ddl\Table::TYPE_DATE;
                break;
            default:
                throw new \Magento\Model\Exception(__("Unknown old style column type definition: {$definition}."));
        }

        $result = array(
            'type' => $type,
            'length' => $length,
            'unsigned' => $column['unsigned'],
            'nullable' => $column['is_null'],
            'default' => $column['default'],
            'identity' => stripos($column['extra'], 'auto_increment') !== false
        );

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null.
         * It just means that default value not specified, and we must remove it from column definition.
         */
        if (false === $column['is_null'] && null === $column['default']) {
            unset($result['default']);
        }

        return $result;
    }
}
