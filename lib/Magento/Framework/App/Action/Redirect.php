<?php
/**
 * Redirect action class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Action;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

class Redirect extends AbstractAction
{
    /**
     * Redirect response
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch(RequestInterface $request)
    {
        return $this->_response;
    }
}
