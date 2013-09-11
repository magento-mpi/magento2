<?php
/**
 * REST web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher\Rest;

class Authentication
{
    /** @var \Magento\Webapi\Model\Authorization\RoleLocator */
    protected $_roleLocator;

    /** @var \Magento\Webapi\Model\Rest\Oauth\Server */
    protected $_oauthServer;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Rest\Oauth\Server $oauthServer
     * @param \Magento\Webapi\Model\Authorization\RoleLocator $roleLocator
     */
    public function __construct(
        \Magento\Webapi\Model\Rest\Oauth\Server $oauthServer,
        \Magento\Webapi\Model\Authorization\RoleLocator $roleLocator
    ) {
        $this->_oauthServer = $oauthServer;
        $this->_roleLocator = $roleLocator;
    }

    /**
     * Authenticate user.
     *
     * @throws \Magento\Webapi\Exception If authentication failed
     */
    public function authenticate()
    {
        try {
            $consumer = $this->_oauthServer->authenticateTwoLegged();
            $this->_roleLocator->setRoleId($consumer->getRoleId());
        } catch (\Exception $e) {
            throw new \Magento\Webapi\Exception(
                $this->_oauthServer->reportProblem($e),
                \Magento\Webapi\Exception::HTTP_UNAUTHORIZED
            );
        }
    }
}
