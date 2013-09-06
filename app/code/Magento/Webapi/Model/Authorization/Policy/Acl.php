<?php
/**
 * WebAPI ACL Policy
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Policy_Acl extends Magento_Authorization_Policy_Acl
{
    /**
     * @param Magento_Acl_Builder $aclBuilder
     */
    public function __construct(Magento_Acl_Builder $aclBuilder)
    {
        parent::__construct($aclBuilder);
    }
}