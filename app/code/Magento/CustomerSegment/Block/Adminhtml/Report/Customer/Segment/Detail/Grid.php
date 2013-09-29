<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segments Detail grid
 *
 * @category   Magento
 * @package    Magento_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Report\Customer\Segment\Detail;

class Grid
    extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Instantiate collection and set required data joins
     *
     * @return \Magento\CustomerSegment\Block\Adminhtml\Report\Customer\Segment\Detail\Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->load();
        return $this->getCollection();
    }
}
