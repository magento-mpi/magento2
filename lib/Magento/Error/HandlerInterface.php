<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of Error Handler
 */
namespace Magento\Error;

interface HandlerInterface
{
    /**
     * Error handler callback method
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @return bool
     */
    public function handler($errorNo, $errorStr, $errorFile, $errorLine);

    /**
     * Process exception
     *
     * @param \Exception $exception
     * @param string[] $params
     * @return void
     */
    public function processException(\Exception $exception, array $params = array());
}
