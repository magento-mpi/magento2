<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Howto;

use Magento\Doc\App\Controller\AbstractAction;

class Index extends AbstractAction
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', 'howto']);

        $this->_view->renderLayout();
    }
}
