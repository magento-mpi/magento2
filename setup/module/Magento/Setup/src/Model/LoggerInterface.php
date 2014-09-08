<?php
/**
 * Created by PhpStorm.
 * User: japatel
 * Date: 9/8/14
 * Time: 2:39 PM
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
     * @param string $moduleName
     * @return void
     */
    public function logSuccess($moduleName);

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