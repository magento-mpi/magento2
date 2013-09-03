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
 * Country column renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Country
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render country grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data);
            if (empty($name)) {
                $name = $this->escapeHtml($data);
            }
            return $name;
        }
        return null;
    }
}
