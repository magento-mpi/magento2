<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Search\Controller\Adminhtml\Term;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportSearchCsv extends \Magento\Search\Controller\Adminhtml\Term
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Export search report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.report.search.grid', 'grid.export');
        return $this->_fileFactory->create('search.csv', $content->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
