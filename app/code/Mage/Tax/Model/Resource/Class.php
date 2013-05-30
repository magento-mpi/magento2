<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax class resource
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Class extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    public function _construct()
    {
        $this->_init('tax_class', 'class_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Tax_Model_Resource_Class
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('class_type', 'class_name'),
            'title' => Mage::helper('Mage_Tax_Helper_Data')->__('Something went wrong saving this tax class because a class with the same name already exists.'),
        ));
        return $this;
    }
}