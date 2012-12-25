<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Font size source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_FontSize extends Mage_Core_Model_Abstract
{
    /**
     * Returns all available options with titles
     *
     * @return array
     */
    public function toOptionArray()
    {
        $sizesOptions = array();
        $sizes = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')->getFontSizesArray();

        foreach ($sizes as $size) {
            $sizesOptions[$size] = $size;
        }

        return $sizesOptions;
    }
}
