<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

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
