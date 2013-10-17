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
namespace Magento\Sales\Block\Adminhtml\Items\Renderer;

class Configurable extends  \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{

    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
