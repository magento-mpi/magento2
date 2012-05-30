<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local resource helper.
 * Need to disable all functionality for local database adapter
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_ResourceHelper extends Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Escapes and quotes LIKE value.
     * Stating escape symbol in expression is not required, because we use standard MySQL escape symbol.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function addLikeEscape($value, $options = array())
    {
        $value = $this->escapeLikeValue($value, $options);
        return new Zend_Db_Expr($this->_getReadAdapter()->quote($value));
    }
}
