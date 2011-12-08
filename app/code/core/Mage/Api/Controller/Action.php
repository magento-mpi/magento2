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
 * Base api controller
 *
 * @category   Mage
 * @package    Mage_Api
*/
class Mage_Api_Controller_Action extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        $this->getLayout()->setArea('adminhtml');
        Mage::app()->setCurrentStore('admin');
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Do not start standart session
        parent::preDispatch();
        return $this;
    }

    /**
     * Retrive webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('Mage_Api_Model_Server');
    }
}
