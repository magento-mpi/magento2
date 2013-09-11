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
 * Adminhtml sales order item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Items\Renderer;

class DefaultRenderer extends \Magento\Adminhtml\Block\Sales\Items\AbstractItems
{
    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
