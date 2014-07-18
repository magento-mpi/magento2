<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Subscriber;

use \Magento\Framework\App\ResponseInterface;

class ExportCsv extends \Magento\Newsletter\Controller\Adminhtml\Subscriber
{
    /**
     * Export subscribers grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'subscribers.csv';
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.newslettrer.subscriber.grid', 'grid.export');

        return $this->_fileFactory->create(
            $fileName,
            $content->getCsvFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }
}
