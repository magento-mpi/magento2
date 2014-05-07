<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\PreProcessor;

/**
 * Default Error Handler for less pre-processing
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(\Magento\Framework\Logger $logger)
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
