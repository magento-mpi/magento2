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
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_INVOICE    => $this->_getHelper()->__('Invoice'),
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_CREDITMEMO => $this->_getHelper()->__('Credit Memo'),
            Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_SHIPMENT   => $this->_getHelper()->__('Shipment'),
        );
    }

    /**
     * Proxy to helper's translator
     *
     * @param string $text
     * @return string Translated text
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }
}
