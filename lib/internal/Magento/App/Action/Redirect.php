<?php
/**
 * Redirect action class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

use \Magento\App\RequestInterface;
use \Magento\App\ResponseInterface;

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
