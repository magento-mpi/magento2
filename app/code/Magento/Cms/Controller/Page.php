<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller;

/**
 * CMS Page controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Page extends \Magento\Framework\App\Action\Action
{
    /**
     * View CMS page action
     *
     * @return void
     */
    public function viewAction()
    {
        $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('noroute');
        }
    }
}
