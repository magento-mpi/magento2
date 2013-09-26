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
class Magento_Downloadable_Controller_Adminhtml_Downloadable_Product_Edit
    extends Magento_Adminhtml_Controller_Catalog_Product
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
                     'Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable',
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
        $helper = $this->_objectManager->get('Magento_Downloadable_Helper_Download');
        /* @var $helper Magento_Downloadable_Helper_Download */

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
        /** @var Magento_Downloadable_Model_Link $link */
        $link = $this->_createLink()->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getLinkUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = $this->_objectManager->get('Magento_Downloadable_Helper_File')->getFilePath(
                    $this->_getLink()->getBasePath(),
                    $link->getLinkFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (Magento_Core_Exception $e) {
                $this->_getCustomerSession()->addError(__('Something went wrong while getting the requested content.'));
            }
        }
        exit(0);
    }

    /**
     * @return Magento_Downloadable_Model_Link
     */
    protected function _getLink()
    {
        return $this->_objectManager->get('Magento_Downloadable_Model_Link');
    }

    /**
     * @return Magento_Downloadable_Model_Link
     */
    protected function _createLink()
    {
        return $this->_objectManager->create('Magento_Downloadable_Model_Link');
    }
}
