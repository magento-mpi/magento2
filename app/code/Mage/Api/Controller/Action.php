<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic API controller
 */
class Mage_Api_Controller_Action extends Magento_Core_Controller_Front_Action
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'frontend';

    /**
     * Use 'admin' store and prevent the session from starting
     *
     * @return Mage_Api_Controller_Action
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
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('Mage_Api_Model_Server');
    }
}
