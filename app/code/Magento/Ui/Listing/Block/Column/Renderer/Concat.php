<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer concat
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

class Concat extends \Magento\Ui\Listing\Block\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $dataArr = array();
        foreach ($this->getColumn()->getIndex() as $index) {
            if ($data = $row->getData($index)) {
                $dataArr[] = $data;
            }
        }
        $data = join($this->getColumn()->getSeparator(), $dataArr);
        // TODO run column type renderer
        return $data;
    }
}
