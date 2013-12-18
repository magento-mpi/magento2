<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid item renderer number
 *
 * @category   Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Tax\Block\Adminhtml\Rate\Grid\Renderer;

class Data extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected function _getValue (\Magento\Object $row)
    {
        $data = parent::_getValue($row);
        if (intval($data) == $data) {
            return (string) number_format($data, 2);
        }
        if (!is_null($data)) {
            return $data * 1;
        }
        return $this->getColumn()->getDefault();
    }
}
