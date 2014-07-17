<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Cache;

use Magento\Framework\Model\Exception;

class CleanImages extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Clean JS/css files cache
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_objectManager->create('Magento\Catalog\Model\Product\Image')->clearCache();
            $this->_eventManager->dispatch('clean_catalog_images_cache_after');
            $this->messageManager->addSuccess(__('The image cache was cleaned.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('An error occurred while clearing the image cache.'));
        }
        $this->_redirect('adminhtml/*');
    }
}
