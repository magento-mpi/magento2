<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer number
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Number
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;

    /**
     * Returns value of the row
     *
     * @param \Magento\Object $row
     * @return mixed|string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($value > 0) ? '+' : '';
            if ($sign) {
                $value = $sign . $value;
            }
            return $value ? $value : '0'; // fixed for showing zero in grid
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Renders CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' col-number';
    }

}
