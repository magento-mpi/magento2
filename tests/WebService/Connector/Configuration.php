<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class WebService_Connector_Configuration
{
    private static $_credentials = -1;

    private static function _init()
    {
        if (-1 === self::$_credentials) {
            Mage::app();

            // check if url available
            $url = Mage::getStoreConfig('web/unsecure/base_url', 0);
            if (empty($url) || '{{base_url}}' === $url) {
                throw new Exception('Base URL must be specified in Magento.');
            }

            // create api role with full access
            $role = Mage::getModel("api/roles")
                    ->setId(false)
                    ->setName(uniqid())
                    ->setPid(false)
                    ->setRoleType('G')
                    ->save();
            Mage::getModel("api/rules")
                ->setRoleId($role->getId())
                ->setResources(array('all'))
                ->saveRel();

            // create api user with pseudo-random id
            $id = 'u' . preg_replace('/[^0-9]/', '', microtime());
            $roles = array($role->getId());
            $user = Mage::getModel('api/user')->setData(array (
                'username'             => $id,
                'firstname'            => $id,
                'lastname'             => $id,
                'email'                => "{$id}@example.com",
                'api_key'              => $id,
                'api_key_confirmation' => $id,
                'is_active'            => 1,
                'user_roles'           => '',
                'page'                 => '1',
                'limit'                => '20',
                'assigned_user_role'   => '',
                'role_name'            => '',
                'roles'                => $roles,
            ))->save();
            $user->setRoleIds($roles)
                 ->setRoleUserId($user->getUserId())
                 ->saveRelations();

            self::$_credentials = new WebService_Connector_Credentials($role, $user, $id, $url);
        }
    }

    public static function getApiLogin()
    {
        self::_init();
        return self::$_credentials->getApiKey();
    }

    public static function getApiPassword()
    {
        self::_init();
        return self::$_credentials->getApiConfirmation();
    }

    public static function getRpcApiUrl()
    {
        self::_init();
        return self::$_credentials->getBaseUrl() . 'api/xmlrpc/';
    }

    public static function getSoapApiUrl()
    {
        self::_init();
        return self::$_credentials->getBaseUrl() . 'api/soap/?wsdl';
    }
}
