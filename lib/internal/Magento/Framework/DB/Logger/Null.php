<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\DB\Logger;

class Null implements \Magento\Framework\DB\LoggerInterface
{
    /**
     * {@inheritdoc}
     */
    public function log($str)
    {}

    /**
     * {@inheritdoc}
     */
    public function logStats($type, $sql, $bind = [], $result = null)
    {}

    /**
     * {@inheritdoc}
     */
    public function logException(\Exception $e)
    {}

    /**
     * {@inheritdoc}
     */
    public function startTimer()
    {}
}
