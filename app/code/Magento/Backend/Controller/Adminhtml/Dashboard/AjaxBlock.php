<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

class AjaxBlock extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * @return void
     */
    public function execute()
    {
        $output = '';
        $blockTab = $this->getRequest()->getParam('block');
        $blockClassSuffix = str_replace(
            ' ',
            '\\',
            ucwords(str_replace('_', ' ', $blockTab))
        );
        if (in_array($blockTab, array('tab_orders', 'tab_amounts', 'totals'))) {
            $output = $this->_view->getLayout()->createBlock(
                'Magento\\Backend\\Block\\Dashboard\\' . $blockClassSuffix
            )->toHtml();
        }
        $this->getResponse()->setBody($output);
        return;
    }
}
