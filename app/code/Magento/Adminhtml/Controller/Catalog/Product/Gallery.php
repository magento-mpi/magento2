<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product gallery controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Catalog\Product;

class Gallery extends \Magento\Adminhtml\Controller\Action
{
    public function uploadAction()
    {
        try {
            $uploader = \Mage::getModel('Magento\Core\Model\File\Uploader', array('fileId' => 'image'));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $imageAdapter = $this->_objectManager->get('Magento\Core\Model\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                \Mage::getSingleton('Magento\Catalog\Model\Product\Media\Config')->getBaseTmpMediaPath()
            );

            $this->_eventManager->dispatch('catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $this
            ));

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = \Mage::getSingleton('Magento\Catalog\Model\Product\Media\Config')
                ->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';

        } catch (\Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            );
        }

        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
