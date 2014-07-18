<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Page;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * View CMS page action
     *
     * @return void
     */
    public function execute()
    {
        $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('noroute');
        }
    }
}
