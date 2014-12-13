<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Items Tab in Edit RMA form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab;

class Items extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Get Header Text for Order Selection
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Items');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Return Items');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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
