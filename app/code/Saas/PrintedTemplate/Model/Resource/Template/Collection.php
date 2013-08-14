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
 * Templates collection.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Resource_Template_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor. Initialize collection's model.
     */
    protected function _construct()
    {
        $this->_init(
            'Saas_PrintedTemplate_Model_Template',
            'Saas_PrintedTemplate_Model_Resource_Template'
        );
    }

    /**
     * Build array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'name');
    }
}
