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
 * Product mass attribute update websites tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Action\Attribute\Tab;

class Websites
    extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    public function getWebsiteCollection()
    {
        return \Mage::app()->getWebsites();
    }

    public function getGroupCollection(\Magento\Core\Model\Website $website)
    {
        return $website->getGroups();
    }

    public function getStoreCollection(\Magento\Core\Model\Store\Group $group)
    {
        return $group->getStores();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return __('Websites');
    }

    public function getTabTitle()
    {
        return __('Websites');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
