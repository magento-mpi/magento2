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
}
