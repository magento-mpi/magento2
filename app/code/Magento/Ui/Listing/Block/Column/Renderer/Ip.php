<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Long INT to IP renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

class Ip extends \Magento\Ui\Listing\Block\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }
}
