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
 * Adminhtml customer orders grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

class Reviews extends \Magento\Adminhtml\Block\Review\Grid
{
    /**
     * Hide grid mass action elements
     *
     * @return \Magento\Customer\Block\Adminhtml\Edit\Tab\Reviews
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Determine ajax url for grid refresh
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customer/*/productReviews', array('_current' => true));
    }
}
