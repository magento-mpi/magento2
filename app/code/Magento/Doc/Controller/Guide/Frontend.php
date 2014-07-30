<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Guide;

use Magento\Doc\App\Controller\AbstractAction;

class Frontend extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', 'guide_frontend']);
        $this->_view->renderLayout();
    }
}
