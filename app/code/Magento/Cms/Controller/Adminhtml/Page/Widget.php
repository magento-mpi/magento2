<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml\Page;

/**
 * Controller for CMS Page Link Widget plugin
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Widget extends \Magento\Backend\App\Action
{
    /**
     * Chooser Source action
     *
     * @return void
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->_view->getLayout()->createBlock(
            'Magento\Cms\Block\Adminhtml\Page\Widget\Chooser',
            '',
            array('data' => array('id' => $uniqId))
        );
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}
