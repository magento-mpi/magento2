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
class Saas_PrintedTemplate_Model_Source_FontSize extends Magento_Core_Model_Abstract
{
    /**
     * Returns all available options with titles
     *
     * @return array
     */
    public function toOptionArray()
    {
        $sizesOptions = array();
        $sizes = $this->_getConfigModelSingeleton()->getFontSizesArray();

        foreach ($sizes as $size) {
            $sizesOptions[$size] = $size;
        }

        return $sizesOptions;
    }

    /**
     * Returns Config model
     *
     * @return  Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfigModelSingeleton()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Config');
    }
}
