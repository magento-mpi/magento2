<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Controller\Download;

use Magento\Framework\App\ResponseInterface;
use Magento\Downloadable\Helper\Download as DownloadHelper;

class LinkSample extends \Magento\Downloadable\Controller\Download
{
    /**
     * Download link's sample action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $linkId = $this->getRequest()->getParam('link_id', 0);
        /** @var \Magento\Downloadable\Model\Link $link */
        $link = $this->_objectManager->create('Magento\Downloadable\Model\Link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getSampleType() == DownloadHelper::LINK_TYPE_URL) {
                $resource = $link->getSampleUrl();
                $resourceType = DownloadHelper::LINK_TYPE_URL;
            } elseif ($link->getSampleType() == DownloadHelper::LINK_TYPE_FILE) {
                $resource = $this->_objectManager->get(
                    'Magento\Downloadable\Helper\File'
                )->getFilePath(
                    $this->_getLink()->getBaseSamplePath(),
                    $link->getSampleFile()
                );
                $resourceType = DownloadHelper::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Sorry, there was an error getting requested content. Please contact the store owner.')
                );
            }
        }
        return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
