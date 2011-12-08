<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template resource collection
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Template_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('Mage_XmlConnect_Model_Template', 'Mage_XmlConnect_Model_Resource_Template');
    }

    /**
     * Initialize collection select
     *
     * @return Mage_XmlConnect_Model_Resource_Template_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinApplicationName();
        return $this;
    }

    /**
     * Join Application Name to collection
     *
     * @return Mage_XmlConnect_Model_Resource_Template_Collection
     */
    protected function _joinApplicationName()
    {
        $this->getSelect()->joinLeft(
            array('app' => $this->getTable('xmlconnect_application')),
            'app.application_id = main_table.application_id',
            array('app_name' => 'app.name', 'app_code' => 'app.code')
        );
        return $this;
    }
}
