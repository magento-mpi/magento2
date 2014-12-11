<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * Download archive file
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $archive = $this->_archiveFactory->create()->loadByBaseName($this->getRequest()->getParam('basename'));
        if ($archive->getFilename()) {
            return $this->_fileFactory->create(
                $archive->getBaseName(),
                $archive->getContents(),
                DirectoryList::VAR_DIR,
                $archive->getMimeType()
            );
        }
    }
}
