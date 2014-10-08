<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

use \Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Viewfile extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Retrieve image MIME type by its extension
     *
     * @param string $extension
     * @return string
     */
    protected function _getPlainImageMimeType($extension)
    {
        $mimeTypeMap = array('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png');
        $contentType = 'application/octet-stream';
        if (isset($mimeTypeMap[$extension])) {
            $contentType = $mimeTypeMap[$extension];
        }
        return $contentType;
    }

    /**
     * Action for view full sized item attribute image
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $fileName = null;
        $plain = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $fileName   = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->urlDecode($this->getRequest()->getParam('file'));
        } elseif ($this->getRequest()->getParam('image')) {
            // show plain image
            $fileName = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->urlDecode(
                $this->getRequest()->getParam('image')
            );
            $plain = true;
        } else {
            throw new NotFoundException();
        }

        $filePath = sprintf('rma_item/%s', $fileName);
        if (!$this->readDirectory->isExist($filePath)) {
            throw new NotFoundException();
        }

        if ($plain) {
            /** @var $readFile \Magento\Framework\Filesystem\File\Read */
            $readFile = $this->readDirectory->openFile($filePath);
            $contentType = $this->_getPlainImageMimeType(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)));
            $fileStat = $this->readDirectory->stat($filePath);
            $this->getResponse()->setHttpResponseCode(
                200
            )->setHeader(
                'Pragma',
                'public',
                true
            )->setHeader(
                'Content-type',
                $contentType,
                true
            )->setHeader(
                'Content-Length',
                $fileStat['size']
            )->setHeader(
                'Last-Modified',
                date('r', $fileStat['mtime'])
            )->clearBody();
            $this->getResponse()->sendHeaders();

            echo $readFile->read($fileStat['size']);
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_fileFactory->create(
                $name,
                array('type' => 'filename', 'value' => $this->readDirectory->getAbsolutePath($filePath)),
                DirectoryList::MEDIA
            )->sendResponse();
        }

        exit;
    }
}
