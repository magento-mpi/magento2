<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\App\Controller;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return \Magento\Framework\Message\ManagerInterface
     */
    protected function getMessageManager()
    {
        return $this->messageManager;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->isDispatched() && $request->getActionName() !== 'denied' && !$this->_isAllowed()) {
            $this->_forward('denied');
            return $this->_response;
        }

        return parent::dispatch($request);
    }
}
