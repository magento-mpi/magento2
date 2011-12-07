<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging history tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Widget_Grid_Column_Renderer_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * replacing ip from long to 4-digits value.
     *
     * @param Varien_Object row   row with 'ip' item
     *
     * @return string - replaced ip value.
     */
    public function render(Varien_Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }
}
