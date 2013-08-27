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
class Magento_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Magento_Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }

}
