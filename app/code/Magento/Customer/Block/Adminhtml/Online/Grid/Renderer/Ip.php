<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Online\Grid\Renderer;

/**
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ip extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }

}
