<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Online\Grid\Renderer;

/**
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ip extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }
}
