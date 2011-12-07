<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Translate extends Mage_Core_Helper_Abstract
{
    /**
     * Save translation data to database for specific area
     *
     * @param array  $translate
     * @param string $area
     * @param string $returnType
     * @return string
     */
    public function apply($translate, $area, $returnType = 'json')
    {
        try {
            if ($area) {
                Mage::getDesign()->setArea($area);
            }
            Mage::getModel('Mage_Core_Model_Translate_Inline')->processAjaxPost($translate);
            return $returnType == 'json' ? "{success:true}" : true;
        } catch (Exception $e) {
            return $returnType == 'json' ? "{error:true,message:'" . $e->getMessage() . "'}" : false;
        }
    }

    /**
     * Sets escaping start marker which then processed by inline translation model
     *
     * @see Mage_Core_Model_Translate_Inline::_escapeInline()
     * @param string $escapeChar Char to escape (default = ')
     * @return string
     */
    public function inlineEscapeStartMarker($escapeChar = "'")
    {
        $escapeChar = str_replace('"', '\"', $escapeChar);
        return "{{escape={$escapeChar}}}";
    }

    /**
     * Sets escaping end marker which then processed by inline translation model
     *
     * @see Mage_Core_Model_Translate_Inline::_escapeInline()
     * @return string
     */
    public function inlineEscapeEndMarker()
    {
        return '{{escape}}';
    }
}
