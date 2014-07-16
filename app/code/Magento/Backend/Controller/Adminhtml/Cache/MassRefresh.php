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

class MassRefresh extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Mass action for cache refresh
     *
     * @return void
     */
    public function execute()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            if (!is_array($types)) {
                $types = array();
            }
            $this->_validateTypes($types);
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
                $this->_eventManager->dispatch('adminhtml_cache_refresh_type', array('type' => $type));
                $updatedTypes++;
            }
            if ($updatedTypes > 0) {
                $this->messageManager->addSuccess(__("%1 cache type(s) refreshed.", $updatedTypes));
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('An error occurred while refreshing cache.'));
        }
        $this->_redirect('adminhtml/*');
    }
}
