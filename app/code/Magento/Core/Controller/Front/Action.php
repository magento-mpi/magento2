<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic frontend controller
 */
class Magento_Core_Controller_Front_Action extends Magento_Core_Controller_Varien_Action
{
    /**
     * Session namespace to refer in other places
     */
    const SESSION_NAMESPACE = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * Remember the last visited url in the session
     *
     * @return Magento_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            Mage::getSingleton('Magento_Core_Model_Session')->setLastUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        }
        return $this;
    }
}
