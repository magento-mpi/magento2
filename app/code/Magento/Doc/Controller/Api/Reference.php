<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Api;

use Magento\Doc\App\Controller\AbstractAction;

class Reference extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', 'api_reference']);

        $this->_view->renderLayout();
    }
}
