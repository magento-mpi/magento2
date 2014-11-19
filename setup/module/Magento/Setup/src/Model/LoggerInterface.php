<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

/**
 * Interface to Log Message in Setup
 *
 * @package Magento\Setup\Model
 */
interface LoggerInterface
{
    /**
     * Logs success message
     *
     * @param string $message
     * @return void
     */
    public function logSuccess($message);

    /**
     * Logs error message
     *
     * @param \Exception $e
     * @return void
     */
    public function logError(\Exception $e);


    /**
     * Logs a message
     *
     * @param string $message
     * @return void
     */
    public function log($message);

    /**
     * Logs meta information
     *
     * @param string $message
     * @return void
     */
    public function logMeta($message);
}
