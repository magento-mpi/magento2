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
 * Country column renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Country
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render country grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
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
