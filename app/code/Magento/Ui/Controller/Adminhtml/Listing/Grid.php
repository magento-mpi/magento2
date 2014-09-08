<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Listing;

/**
 * Class Grid
 */
class Grid extends \Magento\Backend\App\Action
{
    /**
     * Product grid for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $handles = [
            'default',
            $this->getLayoutHandle()
        ];
        $this->_view->loadLayout($handles);
        $this->_view->renderLayout();
    }

    protected function getLayoutHandle()
    {
        return$this->_request->getParam('namespace');
    }
}
