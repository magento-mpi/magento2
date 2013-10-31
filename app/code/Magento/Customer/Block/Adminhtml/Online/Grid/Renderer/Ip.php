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
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Online\Grid\Renderer;

class Ip extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }

}
