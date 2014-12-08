<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFixture\Controller\Adminhtml;

use Magento\Framework\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Mock index controller class
 */
class Noroute implements \Magento\Framework\App\ActionInterface
{
    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws Action\NotFoundException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch(RequestInterface $request)
    {
    }

    /**
     * Get Response object
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
    }
}
