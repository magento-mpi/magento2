<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor;

/**
 * Error handler interface
 */
interface ErrorHandlerInterface
{
    /**
     * Process an exception which was thrown during processing a less instructions
     *
     * @param \Exception $e
     * @return void
     */
    public function processException(\Exception $e);
}