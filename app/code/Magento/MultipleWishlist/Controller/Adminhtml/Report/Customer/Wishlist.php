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

class Wishlist extends \Magento\Backend\App\Action
{
    /**
     * Backend auth session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_backendAuthSession = $backendAuthSession;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Init layout and add breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_MultipleWishlist::report_customers_wishlist')
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
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('wishlist');
    }

    /**
     * Wishlist view action
     *
     * @return void
     */
    public function wishlistAction()
    {
        $this->_title->add(__("Customer Wish List Report"));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Export Excel Action
     *
     * @return \Magento\App\ResponseInterface
     */
    public function exportExcelAction()
    {
        $this->_view->loadLayout();
        $fileName = 'customer_wishlists.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock =
            $this->_view
                ->getLayout()
                ->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export Csv Action
     *
     * @return \Magento\App\ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'customer_wishlists.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.block.report.customer.wishlist.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\App\Filesystem::VAR_DIR
        );
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
