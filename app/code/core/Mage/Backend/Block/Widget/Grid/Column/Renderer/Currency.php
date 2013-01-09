<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer currency
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Backend_Block_Widget_Grid_Column_Renderer_Currency
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Price
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = (float)$row->getData($this->getColumn()->getIndex())) {
            $sign = $this->_shouldShowNumberSign() && ($data > 0) ? '+' : '';
            return $sign . parent::render($row);
        }
        return $this->getColumn()->getDefault();
    }
    /**
     * Check if number sign should be shown
     *
     * @return bool|null
     */
    protected function _shouldShowNumberSign()
    {
        return $this->getColumn()->hasShowNumberSign()?
            (bool)(int)(bool)(int)$this->getColumn()->getShowNumberSign() : false;
    }
}
