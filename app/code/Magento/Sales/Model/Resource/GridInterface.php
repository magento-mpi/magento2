<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource;

/**
 * Interface GridInterface
 */
interface GridInterface
{
    /**
     * @return string
     */
    public function getGridTableName();

    /**
     * @param int|string $value
     * @param null|string $field
     * @return \Zend_Db_Statement_Interface
     */
    public function refresh($value, $field = null);

    /**
     * @param int|string $value
     * @param null|string $field
     * @return int
     */
    public function purge($value, $field = null);
}
