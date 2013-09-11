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
namespace Magento\Core\Controller\Front;

class Action extends \Magento\Core\Controller\Varien\Action
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
     * @return \Magento\Core\Controller\Front\Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            \Mage::getSingleton('Magento\Core\Model\Session')->setLastUrl(\Mage::getUrl('*/*/*', array('_current' => true)));
        }
        return $this;
    }
}
