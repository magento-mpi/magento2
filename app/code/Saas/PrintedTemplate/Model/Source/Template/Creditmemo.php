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
 * Adminhtml config printed credit memo template source
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_Template_Creditmemo extends Saas_PrintedTemplate_Model_Source_Template_Abstract
{
    /**
     * Returns Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_CREDITMEMO
     * @see Saas_PrintedTemplate_Model_Source_Template_Abstract::_getEntityType()
     */
    protected function _getEntityType()
    {
        return Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_CREDITMEMO;
    }
}
