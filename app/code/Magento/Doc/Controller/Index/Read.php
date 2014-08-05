<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Index;

use Magento\Doc\App\Controller\AbstractAction;

/**
 * Class Read
 * @package Magento\Doc\Controller\Dictionary
 */
class Read extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', $this->_request->getParam('ui_handle')]);

        $this->_view->renderLayout();
    }
}
