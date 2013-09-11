<?php
/**
 * Web API authorization model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

class Authorization
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\AuthorizationInterface $authorization
    ) {
        $this->_authorization = $authorization;
    }

    /**
     * Check permissions on specific resource in ACL.
     *
     * @param string $resource
     * @param string $method
     * @throws \Magento\Webapi\Exception
     */
    public function checkResourceAcl($resource, $method)
    {
        $coreAuthorization = $this->_authorization;
        if (!$coreAuthorization->isAllowed($resource . \Magento\Webapi\Model\Acl\Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new \Magento\Webapi\Exception(
                __('Access to resource is forbidden.'),
                \Magento\Webapi\Exception::HTTP_FORBIDDEN
            );
        }
    }
}
