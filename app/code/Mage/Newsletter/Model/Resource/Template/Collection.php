<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter templates collection
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Resource_Template_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Newsletter_Model_Template', 'Mage_Newsletter_Model_Resource_Template');
    }

    /**
     * Load only actual template
     *
     * @return Mage_Newsletter_Model_Resource_Template_Collection
     */
    public function useOnlyActual()
    {
        $this->addFieldToFilter('template_actual', 1);

        return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
