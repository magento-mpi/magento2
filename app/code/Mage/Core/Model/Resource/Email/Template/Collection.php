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
 * Templates collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Email_Template_Collection extends  Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;

    /**
     * Define resource table
     *
     */
    public function _construct()
    {
        $this->_init('Mage_Core_Model_Email_Template', 'Mage_Core_Model_Resource_Email_Template');
        $this->_templateTable = $this->getMainTable();
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
