<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

/**
 * Store selector
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated
 */
class Store extends \Magento\Backend\Block\Store\Switcher
{
    /**
     * Retrieve id of currently selected store
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            $this->setData('store_id', (int)$this->getRequest()->getPost('preview_selected_store'));
        }
        return $this->getData('store_id');
    }
}
