<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Error;

require_once __DIR__ . '/../../app/bootstrap.php';
require_once 'processor.php';

/**
 * Error processor factory
 */
class ProcessorFactory
{
    /**
     * Create Processor
     *
     * @return Processor
     */
    public function createProcessor()
    {
        $locatorFactory = new \Magento\Framework\App\ObjectManagerFactory();
        $locator = $locatorFactory->create(BP, $_SERVER);
        $response = $locator->create('\Magento\Framework\App\Response\Http');
        return new Processor($response);
    }
}
