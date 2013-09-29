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
 * Related banners edit tab for promo catalog rule edit page
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Promo\Catalogrule\Edit\Tab;

class Banners
extends \Magento\Adminhtml\Block\Text\ListText
implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
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
