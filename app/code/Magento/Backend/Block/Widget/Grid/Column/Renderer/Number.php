<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

/**
 * Backend grid item renderer number
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Number extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 100;

    /**
     * Returns value of the row
     *
     * @param \Magento\Framework\Object $row
     * @return mixed|string
     */
    protected function _getValue(\Magento\Framework\Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && $value > 0 ? '+' : '';
            if ($sign) {
                $value = $sign . $value;
            }
            // fixed for showing zero in grid
            return $value ? $value : '0';
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
