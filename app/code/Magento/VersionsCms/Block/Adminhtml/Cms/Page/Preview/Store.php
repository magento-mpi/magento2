<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Store selector
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

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
