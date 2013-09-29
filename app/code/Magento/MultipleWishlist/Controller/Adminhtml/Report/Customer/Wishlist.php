<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist reports controller
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer;

class Wishlist extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Backend auth session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Construct
     *
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_backendAuthSession = $backendAuthSession;
        parent::__construct($context);
    }

    /**
     * Init layout and add breadcrumbs
     *
     * @return \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_MultipleWishlist::report_customers_wishlist')
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Customers'),
                __('Customers')
            );
        return $this;
    }

    /**
     * Index Action.
     * Forward to Wishlist Action
     */
    public function indexAction()
    {
        $this->_forward('wishlist');
    }

    /**
     * Wishlist view action
     */
    public function wishlistAction()
    {
        $this->_title(__("Customer Wish List Report"));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Export Excel Action
     */
    public function exportExcelAction()
    {
        $this->loadLayout();
        $fileName = 'customer_wishlists.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Export Csv Action
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'customer_wishlists.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
 	 	$exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
 	 	$this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Retrieve admin session model
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    protected function _getAdminSession()
    {
        return $this->_backendAuthSession;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return  $this->_authorization->isAllowed('Magento_MultipleWishlist::wishlist');
    }
}
