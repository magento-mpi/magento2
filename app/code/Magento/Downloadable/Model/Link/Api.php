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
 * Downloadable links API model
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model\Link;

class Api extends \Magento\Catalog\Model\Api\Resource
{
    /**
     * Return validator instance
     *
     * @return \Magento\Downloadable\Model\Link\Api\Validator
     */
    protected function _getValidator()
    {
        return \Mage::getSingleton('Magento\Downloadable\Model\Link\Api\Validator');
    }

    /**
     * Decode file from base64 and upload it to donwloadable 'tmp' folder
     *
     * @param array $fileInfo
     * @param string $type
     * @return string
     */
    protected function _uploadFile($fileInfo, $type)
    {
        $tmpPath = '';
        if ($type == 'sample') {
            $tmpPath = \Magento\Downloadable\Model\Sample::getBaseTmpPath();
        } elseif ($type == 'link') {
            $tmpPath = \Magento\Downloadable\Model\Link::getBaseTmpPath();
        } elseif ($type == 'link_samples') {
            $tmpPath = \Magento\Downloadable\Model\Link::getBaseSampleTmpPath();
        }

        $result = array();
        try {
            $uploader = \Mage::getModel('Magento\Downloadable\Model\Link\Api\Uploader', array('file' => $fileInfo));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($tmpPath);

            if (isset($result['file'])) {
                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                \Mage::helper('Magento\Core\Helper\File\Storage\Database')->saveFile($fullPath);
            }
        } catch (\Exception $e) {
            if ($e->getMessage() != '') {
                $this->_fault('upload_failed', $e->getMessage());
            } else {
                $this->_fault($e->getCode());
            }
        }

        $result['status'] = 'new';
        $result['name'] = substr($result['file'], strrpos($result['file'], '/')+1);
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(array($result));
    }

    /**
     * Add downloadable content to product
     *
     * @param int|string $productId
     * @param array $resource
     * @param string $resourceType
     * @param string|int|null $store
     * @param string|null $identifierType ('sku'|'id')
     * @return boolean
     */
    public function add($productId, $resource, $resourceType, $store = null, $identifierType = null)
    {
        try {
            $this->_getValidator()->validateType($resourceType);
            $this->_getValidator()->validateAttributes($resource, $resourceType);
        } catch (\Exception $e) {
            $this->_fault('validation_error', $e->getMessage());
        }

        $resource['is_delete'] = 0;
        if ($resourceType == 'link') {
            $resource['link_id'] = 0;
        } elseif ($resourceType == 'sample') {
            $resource['sample_id'] = 0;
        }

        if ($resource['type'] == 'file') {
            if (isset($resource['file'])) {
                $resource['file'] = $this->_uploadFile($resource['file'], $resourceType);
            }
            unset($resource[$resourceType.'_url']);
        } elseif ($resource['type'] == 'url') {
            unset($resource['file']);
        }

        if ($resourceType == 'link' && $resource['sample']['type'] == 'file') {
            if (isset($resource['sample']['file'])) {
                $resource['sample']['file'] = $this->_uploadFile($resource['sample']['file'], 'link_samples');
            }
            unset($resource['sample']['url']);
        } elseif ($resourceType == 'link' && $resource['sample']['type'] == 'url') {
            $resource['sample']['file'] = null;
        }

        $product = $this->_getProduct($productId, $store, $identifierType);
        try {
            $downloadable = array($resourceType => array($resource));
            $product->setDownloadableData($downloadable);
            $product->save();
        } catch (\Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Retrieve downloadable product links
     *
     * @param int|string $productId
     * @param string|int $store
     * @param string $identifierType ('sku'|'id')
     * @return array
     */
    public function items($productId, $store = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $linkArr = array();
        $links = $product->getTypeInstance()->getLinks($product);
        $fileHelper = \Mage::helper('Magento\Downloadable\Helper\File');
        foreach ($links as $item) {
            $tmpLinkItem = array(
                'link_id' => $item->getId(),
                'title' => $item->getTitle(),
                'price' => $item->getPrice(),
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'link_type' => $item->getLinkType(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder()
            );
            $file = $fileHelper->getFilePath(
                \Magento\Downloadable\Model\Link::getBasePath(), $item->getLinkFile()
            );

            if ($item->getLinkFile() && !is_file($file)) {
                \Mage::helper('Magento\Core\Helper\File\Storage\Database')->saveFileToFilesystem($file);
            }

            if ($item->getLinkFile() && is_file($file)) {
                $name = $fileHelper->getFileFromPathFile($item->getLinkFile());
                $tmpLinkItem['file_save'] = array(
                    array(
                        'file' => $item->getLinkFile(),
                        'name' => $name,
                        'size' => filesize($file),
                        'status' => 'old'
                    ));
            }
            $sampleFile = $fileHelper->getFilePath(
                \Magento\Downloadable\Model\Link::getBaseSamplePath(), $item->getSampleFile()
            );
            if ($item->getSampleFile() && is_file($sampleFile)) {
                $tmpLinkItem['sample_file_save'] = array(
                    array(
                        'file' => $item->getSampleFile(),
                        'name' => $fileHelper->getFileFromPathFile($item->getSampleFile()),
                        'size' => filesize($sampleFile),
                        'status' => 'old'
                    ));
            }
            if ($item->getNumberOfDownloads() == '0') {
                $tmpLinkItem['is_unlimited'] = 1;
            }
            if ($product->getStoreId() && $item->getStoreTitle()) {
                $tmpLinkItem['store_title'] = $item->getStoreTitle();
            }
            if ($product->getStoreId() && \Mage::helper('Magento\Downloadable\Helper\Data')->getIsPriceWebsiteScope()) {
                $tmpLinkItem['website_price'] = $item->getWebsitePrice();
            }
            $linkArr[] = $tmpLinkItem;
        }
        unset($item);
        unset($tmpLinkItem);
        unset($links);

        $samples = $product->getTypeInstance()->getSamples($product)->getData();
        return array('links' => $linkArr, 'samples' => $samples);
    }

    /**
     * Remove downloadable product link
     * @param string $linkId
     * @param string $resourceType
     * @return bool
     */
    public function remove($linkId, $resourceType)
    {
        try {
            $this->_getValidator()->validateType($resourceType);
        } catch (\Exception $e) {
            $this->_fault('validation_error', $e->getMessage());
        }

        switch($resourceType) {
            case 'link':
                $downloadableModel = \Mage::getSingleton('Magento\Downloadable\Model\Link');
                break;
            case 'sample':
                $downloadableModel = \Mage::getSingleton('Magento\Downloadable\Model\Sample');
                break;
        }

        $downloadableModel->load($linkId);
        if (is_null($downloadableModel->getId())) {
            $this->_fault('link_was_not_found');
        }

        try {
            $downloadableModel->delete();
        } catch (\Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Return loaded downloadable product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @param  string $identifierType
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct($productId, $store = null, $identifierType = null)
    {
        $product = parent::_getProduct($productId, $store, $identifierType);

        if ($product->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            $this->_fault('product_not_downloadable');
        }

        return $product;
    }
}
