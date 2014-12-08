<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

/**
 * Adminhtml grid item renderer number or blank line
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blanknumber extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Number
{
    /**
     * @param \Magento\Framework\Object $row
     *
     * @return string
     */
    protected function _getValue(\Magento\Framework\Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            return $value ? $value : ''; // fixed for showing blank cell in grid
            /**
             * @todo may be bug in i.e. needs to be fixed
             */
        }
        return $this->getColumn()->getDefault();
    }
}
