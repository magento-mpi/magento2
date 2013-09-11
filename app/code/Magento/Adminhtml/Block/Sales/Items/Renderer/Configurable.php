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

class Configurable extends  Magento_Adminhtml_Block_Sales_Items_Abstract
{

    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
