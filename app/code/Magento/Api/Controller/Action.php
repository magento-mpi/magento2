<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic API controller
 */
class Magento_Api_Controller_Action extends Magento_Core_Controller_Front_Action
{
    /**
     * Use 'admin' store and prevent the session from starting
     *
     * @return Magento_Api_Controller_Action
     */
    public function preDispatch()
    {
        Mage::app()->setCurrentStore('admin');
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1);
        parent::preDispatch();
        return $this;
    }

    /**
     * Retrieve webservice server
     *
     * @return Magento_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('Magento_Api_Model_Server');
    }
}
