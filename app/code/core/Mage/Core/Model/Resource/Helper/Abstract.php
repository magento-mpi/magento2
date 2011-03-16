<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource helper class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Read adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_readAdapter;

    /**
     * Write adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_writeAdapter;

    /**
     * Resource helper module prefix
     *
     * @var string
     */
    protected $_modulePrefix;

    /**
     * Initialize resource helper instance
     *
     * @param string $module
     */
    public function __construct($module)
    {
        $this->_modulePrefix = (string)$module;
    }

    /**
     * Retrieve connection for read data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        if ($this->_readAdapter === null) {
            $this->_readAdapter = $this->_getConnection('read');
        }

        return $this->_readAdapter;
    }

    /**
     * Retrieve connection for write data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if ($this->_writeAdapter === null) {
            $this->_writeAdapter = $this->_getConnection('write');
        }

        return $this->_writeAdapter;
    }

    /**
     * Retrieves connection to the resource
     *
     * @param string $name
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection($name)
    {
        $connection = sprintf('%s_%s', $this->_modulePrefix, $name);
        /** @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');

        return $resource->getConnection($connection);
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
     * - 'position' ('any', 'start', 'end') - expression will be formed so that $value will be found at position within string,
     *     by default when nothing set - string must be fully matched with $value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function escapeLikeValue($value, $options = array())
    {
        $value = str_replace('\\', '\\\\', $value);

        $from = array();
        $to = array();
        if (empty($options['allow_symbol_mask'])) {
            $from[] = '_';
            $to[] = '\_';
        }
        if (empty($options['allow_string_mask'])) {
            $from[] = '%';
            $to[] = '\%';
        }
        if ($from) {
            $value = str_replace($from, $to, $value);
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
     * @return Zend_Db_Expr
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
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function getCILike($field, $value, $options = array())
    {
        $quotedField = $this->_getReadAdapter()->quoteIdentifier($field);
        return new Zend_Db_Expr($quotedField . ' LIKE ' . $this->addLikeEscape($value, $options));
    }
}
