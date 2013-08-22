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
 * Backend grid item renderer concat
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Backend_Block_Widget_Grid_Column_Renderer_Concat
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
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
