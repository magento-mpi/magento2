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
namespace Magento\Api\Controller;

class Action extends \Magento\Core\Controller\Front\Action
{
    /**
     * Use 'admin' store and prevent the session from starting
     *
     * @return \Magento\Api\Controller\Action
     */
    public function preDispatch()
    {
        \Mage::app()->setCurrentStore('admin');
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1);
        parent::preDispatch();
        return $this;
    }

    /**
     * Retrieve webservice server
     *
     * @return \Magento\Api\Model\Server
     */
    protected function _getServer()
    {
        return \Mage::getSingleton('Magento\Api\Model\Server');
    }
}
