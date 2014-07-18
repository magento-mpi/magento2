<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface OrderLoaderInterface
{
    /**
     * Load order
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Magento\Sales\Model\Order
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
