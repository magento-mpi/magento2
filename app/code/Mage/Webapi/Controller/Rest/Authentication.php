<?php
/**
 * REST web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest_Authentication
{
    /** @var Mage_Webapi_Model_Authorization_RoleLocator */
    protected $_roleLocator;

    /** @var Mage_Webapi_Model_Rest_Oauth_Server */
    protected $_oauthServer;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Rest_Oauth_Server $oauthServer
     * @param Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
     */
    public function __construct(
        Mage_Webapi_Model_Rest_Oauth_Server $oauthServer,
        Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
    ) {
        $this->_oauthServer = $oauthServer;
        $this->_roleLocator = $roleLocator;
    }

    /**
     * Authenticate user.
     *
     * @throws Mage_Webapi_Exception If authentication failed
     */
    public function authenticate()
    {
        try {
            $consumer = $this->_oauthServer->authenticateTwoLegged();
            $this->_roleLocator->setRoleId($consumer->getRoleId());
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception(
                $this->_oauthServer->reportProblem($e),
                Mage_Webapi_Exception::HTTP_UNAUTHORIZED
            );
        }
    }
}
