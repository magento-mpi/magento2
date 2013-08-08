<?php
/**
 * Job collection resource
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Job_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize Collection
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Webhook_Model_Job', 'Mage_Webhook_Model_Resource_Job');
    }

    /**
     * Add alias method for Zend limitPage().
     *
     * @return Mage_Webhook_Model_Resource_Job_Collection
     */
    public function setPageLimit()
    {
        $this->_select->limitPage($this->getCurPage(), $this->getPageSize());
        return $this;
    }
}
