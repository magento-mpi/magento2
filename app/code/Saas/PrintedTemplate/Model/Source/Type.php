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
 * Source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_Type
{
    /**
     * Returns all available options with titles
     *
     * @return array
     */
    public function getAllOptions()
    {
        return array(
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_INVOICE    => __('Invoice'),
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_CREDITMEMO => __('Credit Memo'),
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_SHIPMENT   => __('Shipment'),
        );
    }

    /**
     * Returns Data helper
     *
     * @return  Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }
}
