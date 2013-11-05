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

class Forward extends AbstractAction
{
    /**
     * Dispatch controller action
     *
     * @param string $action action name
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch($action)
    {
        $this->_request->setDispatched(false);
    }
}
