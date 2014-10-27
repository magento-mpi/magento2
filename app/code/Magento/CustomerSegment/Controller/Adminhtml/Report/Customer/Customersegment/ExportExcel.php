<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportExcel extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Export Excel Action
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        if ($this->_initSegment()) {
            $fileName = 'customersegment_customers.xml';
            $this->_view->loadLayout();
            $content = $this->_view->getLayout()->getChildBlock('report.customersegment.detail.grid', 'grid.export');
            return $this->_fileFactory->create(
                $fileName,
                $content->getExcelFile($fileName),
                DirectoryList::VAR_DIR
            );
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return;
        }
    }
}
