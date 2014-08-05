<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Help;

use Magento\Doc\App\Controller\AbstractAction;

class Howto extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', 'help_howto']);
        $this->_view->renderLayout();
    }
}
