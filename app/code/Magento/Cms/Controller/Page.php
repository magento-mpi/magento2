<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Page controller
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Controller;

class Page extends \Magento\Core\Controller\Front\Action
{
    /**
     * View CMS page action
     *
     */
    public function viewAction()
    {
        $pageId = $this->getRequest()
            ->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
    }
}
