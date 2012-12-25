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
 * Fonts source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_Font
{
    /**
     * Returns all available options with titles
     *
     * @return array
     */
    public function toOptionArray()
    {
        $fontsOptions = array();
        $fonts = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')->getFontsArray();

        foreach ($fonts as $key => $font) {
            if (isset($font['css'], $font['label'])) {
                $fontsOptions[$key] = Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($font['label']);
            }
        }

        return $fontsOptions;
    }
}
