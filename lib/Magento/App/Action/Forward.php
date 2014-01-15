<?php
/**
 * Forward action class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

use Magento\App\RequestInterface;

class Forward extends AbstractAction
{
    /**
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch(RequestInterface $request)
    {
        $request->setDispatched(false);
        return $this->_response;
    }
}
