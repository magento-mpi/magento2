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
 * Queue resource collection
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Queue_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('Mage_XmlConnect_Model_Queue', 'Mage_XmlConnect_Model_Resource_Queue');
    }

    /**
     * Initialize collection select
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinNames();
        return $this;
    }

    /**
     * Join Template Name and Application Name to collection
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _joinNames()
    {
        $this->_joinTemplateName();
        $this->_joinApplicationName();
        return $this;
    }

   /**
    * Join Template Name to collection
    *
    * @return Mage_XmlConnect_Model_Resource_Queue_Collection
    */
    protected function _joinTemplateName()
    {
        $this->getSelect()->joinLeft(
            array('t' => $this->getTable('xmlconnect_notification_template')),
            't.template_id = main_table.template_id',
            array('template_name' => 't.name')
        );
        return $this;
    }

    /**
     * Join Application Name to collection
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _joinApplicationName()
    {
        $this->getSelect()->joinLeft(
            array('app' => $this->getTable('xmlconnect_application')),
            'app.application_id = t.application_id',
            array('application_name' => 'app.name')
        );
        return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()->where('main_table.status in (?)', array(Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE))
             ->where('main_table.exec_time < ?', Mage::getSingleton('Mage_Core_Model_Date')->gmtDate())
             ->order(new Zend_Db_Expr('main_table.exec_time ' . Zend_Db_Select::SQL_ASC)
        );
        return $this;
    }
}
