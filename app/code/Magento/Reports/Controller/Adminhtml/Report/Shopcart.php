<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart reports admin controller
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

use Magento\App\ResponseInterface;

class Shopcart extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Add reports and shopping cart breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        $this->_addBreadcrumb(__('Shopping Cart'), __('Shopping Cart'));
        return $this;
    }

    /**
     * Customer shopping carts action
     *
     * @return void
     */
    public function customerAction()
    {
        $this->_title->add(__('Customer Shopping Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_customer')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addContent(
                $this->_view
                    ->getLayout()
                    ->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Customer')
            );
        $this->_view->renderLayout();
    }

    /**
     * Export shopcart customer report to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'shopcart_customer.csv';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Customer\Grid')
            ->getCsvFile();

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Export shopcart customer report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'shopcart_customer.xml';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Customer\Grid')
            ->getExcelFile($fileName);

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Products in carts action
     *
     * @return void
     */
    public function productAction()
    {
        $this->_title->add(__('Products in Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_product')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addContent(
                $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Product')
            );
        $this->_view->renderLayout();
    }

    /**
     * Export products report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'shopcart_product.csv';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Product\Grid')
            ->getCsvFile();

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Export products report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'shopcart_product.xml';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Product\Grid')
            ->getExcelFile($fileName);

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Abandoned carts action
     *
     * @return void
     */
    public function abandonedAction()
    {
        $this->_title->add(__('Abandoned Carts'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_shopcart_abandoned')
            ->_addBreadcrumb(__('Abandoned Carts'), __('Abandoned Carts'))
            ->_addContent(
                $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Abandoned')
            );
        $this->_view->renderLayout();
    }

    /**
     * Export abandoned carts report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportAbandonedCsvAction()
    {
        $fileName   = 'shopcart_abandoned.csv';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid')
            ->getCsvFile();

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Export abandoned carts report to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportAbandonedExcelAction()
    {
        $fileName   = 'shopcart_abandoned.xml';
        $content    = $this->_view->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid')
            ->getExcelFile($fileName);

        return $this->_fileFactory->create($fileName, $content);
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return $this->_authorization->isAllowed(null);
                break;
            case 'product':
                return $this->_authorization->isAllowed('Magento_Reports::product');
                break;
            case 'abandoned':
                return $this->_authorization->isAllowed('Magento_Reports::abandoned');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::shopcart');
                break;
        }
    }
}
