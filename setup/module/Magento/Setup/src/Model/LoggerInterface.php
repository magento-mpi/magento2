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
     * Logs installed message
     *
     * @param string $moduleName
     * @return void
     */
    public function logInstalled($moduleName);

    /**
     * Logs error message
     *
     * @param \Exception $e
     * @return void
     */
    public function logError(\Exception $e);


    /**
     * Logs message to log writer
     *
     * @param string $message
     * @return void
     */
    public function log($message);

}
