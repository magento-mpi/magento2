<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Authorization_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * @var Mage_Core_Model_Session
     */
    protected $_session;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_session = isset($data['session']) ?
            $data['session'] :
            Mage::getSingleton('Mage_Core_Model_Session');
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        // TODO: migrate 'webapi_user' to constant in Auth model
        /** @var $session Mage_Core_Model_Session */
        if ($this->_session->hasData('webapi_user')
            and ($user = $this->_session->getData('webapi_user')) instanceof Varien_Object) {
            return $user->getRoleId();
        }
    }
}
