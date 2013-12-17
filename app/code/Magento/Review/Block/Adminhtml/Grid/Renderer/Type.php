<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml review grid item renderer for item type
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Block\Adminhtml\Grid\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Object $row)
    {

        if (is_null($row->getCustomerId())) {
            if ($row->getStoreId() == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
                return __('Administrator');
            } else {
                return __('Guest');
            }
        } elseif ($row->getCustomerId() > 0) {
            return __('Customer');
        }
//		return ($row->getCustomerId() ? __('Customer') : __('Guest'));
    }
}// Class \Magento\Review\Block\Adminhtml\Grid\Renderer\Type END
