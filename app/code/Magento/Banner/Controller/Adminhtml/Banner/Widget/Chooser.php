<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Controller\Adminhtml\Banner\Widget;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->_view->getLayout()->createBlock(
            'Magento\Banner\Block\Adminhtml\Widget\Chooser',
            '',
            ['data' => ['id' => $uniqId]]
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
