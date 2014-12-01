<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data\CollectionBuilder;

use Magento\Framework\Api\AbstractExtensibleObject;

class Field extends AbstractExtensibleObject
{
    const NAME = 'name';
    const ALIAS = 'alias';
    const TABLE_NAME = 'table_name';
    const TABLE_ALIAS = 'table_alias';
    const TABLE_JOIN_TYPE = 'table_join_type';

    /**
     * Returns a name of a field
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Returns an alias of a field
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_get(self::ALIAS);
    }

    /**
     * Returns a name of a field's source table
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_get(self::TABLE_NAME);
    }

    /**
     * Returns an alias of a name of a field's source table
     *
     * @return string
     */
    public function getTableAlias()
    {
        return $this->_get(self::TABLE_ALIAS);
    }

    /**
     * Returns a field's source table join type
     *
     * @return string
     */
    public function getTableJoinType()
    {
        return $this->_get(self::TABLE_JOIN_TYPE);
    }
}
