<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB;

/**
 * DB logger interface
 */
interface LoggerInterface
{
    /**#@+
     * Types of connections to be logged
     */
    const TYPE_CONNECT     = 0;
    const TYPE_TRANSACTION = 1;
    const TYPE_QUERY       = 2;
    /**#@-*/

    /**
     * Adds log record
     *
     * @param string $str
     * @return void
     */
    public function log($str);

    /**
     * @return void
     */
    public function startTimer();

    /**
     * @param int $type
     * @param string $sql
     * @param array $bind
     * @param null $result
     * @return void
     */
    public function logStats($type, $sql, $bind = [], $result = null);

    /**
     * @param \Exception $e
     * @return void
     */
    public function logException(\Exception $e);
}
