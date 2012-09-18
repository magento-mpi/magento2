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
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        // TODO: migrate 'webapi_user' to constant in Auth model
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('Mage_Core_Model_Session');
        if ($session->hasData('webapi_user')
            and ($user = $session->getData('webapi_user')) instanceof Varien_Object) {
            return $user->getRoleId();
        }
    }
}
