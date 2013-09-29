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
 * Adminhtml review grid item renderer for item type
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review\Grid\Renderer;

class Type extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Object $row)
    {

        if (is_null($row->getCustomerId())) {
            if ($row->getStoreId() == \Magento\Core\Model\AppInterface::ADMIN_STORE_ID) {
                return __('Administrator');
            } else {
                return __('Guest');
            }
        } elseif ($row->getCustomerId() > 0) {
            return __('Customer');
        }
//		return ($row->getCustomerId() ? __('Customer') : __('Guest'));
    }
}// Class \Magento\Adminhtml\Block\Review\Grid\Renderer\Type END
