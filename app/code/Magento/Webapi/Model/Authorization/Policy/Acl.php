<?php
/**
 * WebAPI ACL Policy
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Policy_Acl extends \Magento\Authorization\Policy\Acl
{
    /**
     * @param \Magento\Acl\Builder $aclBuilder
     */
    public function __construct(\Magento\Acl\Builder $aclBuilder)
    {
        parent::__construct($aclBuilder);
    }
}