<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml\Grid\Renderer;

/**
 * Adminhtml review grid item renderer for item type
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render review type
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        if ($row->getCustomerId()) {
            return __('Customer');
        }
        if ($row->getStoreId() == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
            return __('Administrator');
        }
        return __('Guest');
    }
}// Class \Magento\Review\Block\Adminhtml\Grid\Renderer\Type END
