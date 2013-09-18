<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml downloadable product edit
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Controller\Adminhtml\Downloadable\Product;

class Edit extends \Magento\Adminhtml\Controller\Catalog\Product
{
    /**
     * Load downloadable tab fieldsets
     *
     */
    public function formAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock(
                     'Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable',
                    'admin.product.downloadable.information')
                ->toHtml()
        );
    }

    /**
     * Download process
     *
     * @param string $resource
     * @param string $resourceType
     */
    protected function _processDownload($resource, $resourceType)
    {
        $helper = $this->_objectManager->get('Magento\Downloadable\Helper\Download');
        /* @var $helper \Magento\Downloadable\Helper\Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }

    /**
     * Download link action
     *
     */
    public function linkAction()
    {
        $linkId = $this->getRequest()->getParam('id', 0);
        $link = \Mage::getModel('Magento\Downloadable\Model\Link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getLinkType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_URL) {
                $resource = $link->getLinkUrl();
                $resourceType = \Magento\Downloadable\Helper\Download::LINK_TYPE_URL;
            } elseif ($link->getLinkType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE) {
                $resource = $this->_objectManager->get('Magento\Downloadable\Helper\File')->getFilePath(
                    \Magento\Downloadable\Model\Link::getBasePath(), $link->getLinkFile()
                );
                $resourceType = \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (\Magento\Core\Exception $e) {
                $this->_getCustomerSession()->addError(__('Something went wrong while getting the requested content.'));
            }
        }
        exit(0);
    }

}
