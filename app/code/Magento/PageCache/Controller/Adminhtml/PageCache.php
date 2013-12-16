<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Controller\Adminhtml;

/**
 * Page cache admin controller
 */
class PageCache extends \Magento\Backend\App\Action
{
    /**
     * Clean external cache action
     *
     * @return void
     */
    public function cleanAction()
    {
        try {
            $pageCacheData = $this->_objectManager->get('Magento\PageCache\Helper\Data');
            if ($pageCacheData->isEnabled()) {
                $pageCacheData->getCacheControlInstance()->clean();
                $this->messageManager->addSuccess(
                    __('The external full page cache has been cleaned.')
                );
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while clearing the external full page cache.')
            );
        }
        $this->_redirect('adminhtml/cache/index');
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_PageCache::page_cache');
    }
}
