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

class CleanMedia extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Clean JS/css files cache
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_objectManager->get('Magento\Framework\View\Asset\MergeService')->cleanMergedJsCss();
            $this->_eventManager->dispatch('clean_media_cache_after');
            $this->messageManager->addSuccess(__('The JavaScript/CSS cache has been cleaned.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('An error occurred while clearing the JavaScript/CSS cache.'));
        }
        $this->_redirect('adminhtml/*');
    }
}
