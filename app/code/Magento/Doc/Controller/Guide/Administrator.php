<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Guide;

use Magento\Doc\App\Controller\AbstractAction;

class Administrator extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', 'guide_administrator']);
        $this->_view->renderLayout();
    }
}
