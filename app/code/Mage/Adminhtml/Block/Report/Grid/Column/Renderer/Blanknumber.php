<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid item renderer number or blank line
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Blanknumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected function _getValue(Magento_Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            return $value ? $value: ''; // fixed for showing blank cell in grid
            /**
             * @todo may be bug in i.e. needs to be fixed
             */
        }
        return $this->getColumn()->getDefault();
    }
}
