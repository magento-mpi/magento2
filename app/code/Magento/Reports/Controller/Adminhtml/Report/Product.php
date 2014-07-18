<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product reports admin controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

class Product extends AbstractReport
{
    /**
     * Add report/products breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(__('Products'), __('Products'));
        return $this;
    }
}
