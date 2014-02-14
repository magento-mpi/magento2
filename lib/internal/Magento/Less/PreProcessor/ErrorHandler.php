<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor;

/**
 * Default Error Handler for less pre-processing
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Logger $logger
     */
    public function __construct(\Magento\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function processException(\Exception $e)
    {
        $this->logger->logException($e);
    }
}