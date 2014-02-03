<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * sales admin controller
 *
 * @category   Magento
 * @package    Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Promo extends Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CatalogRule::promo');
        $this->_addBreadcrumb(__('Promotions'), __('Promo'));
        $this->_view->renderLayout();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogRule::promo');
    }

}
