<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Related banners edit tab for promo sales rule edit page
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners
extends Magento_Adminhtml_Block_Text_List
implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Related Banners');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Related Banners');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
