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
 * Resource model for template.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Resource_Template
    extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model constructor.
     * Initialize connection to database and associated table.
     */
    protected function _construct()
    {
        $this->_init('saas_printed_template', 'template_id');
    }

    /**
     * Prepare data for save
     *
     * @param   Magento_Core_Model_Abstract $object
     * @return  array
     */
    protected function _prepareDataForSave(Magento_Core_Model_Abstract $object)
    {
        $date = now();
        if (!$object->getId() || $object->isObjectNew()) {
            $object->setCreatedAt($date);
        }
        $object->setUpdatedAt($date);

        return parent::_prepareDataForSave($object);
    }
}
