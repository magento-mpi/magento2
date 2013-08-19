<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Directory country format resource model
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Country_Format extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('directory_country_format', 'country_format_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Directory_Model_Resource_Country_Format
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('country_id', 'type'),
            'title' => __('Country and Format Type combination should be unique')
        ));
        return $this;
    }
}
