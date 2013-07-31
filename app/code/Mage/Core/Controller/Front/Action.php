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
 * Generic frontend controller
 */
class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Session namespace to refer in other places
     */
    const SESSION_NAMESPACE = 'frontend';

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * Remember the last visited url in the session
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            Mage::getSingleton('Mage_Core_Model_Session')->setLastUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        }
        return $this;
    }

    /**
     * Translate a phrase
     *
     * @return string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function __()
    {
        return $this->_objectManager->get('Mage_Core_Model_Translate')->translate(func_get_args());
    }
}
