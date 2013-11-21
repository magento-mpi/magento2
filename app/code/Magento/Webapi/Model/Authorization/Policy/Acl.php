<?php
/**
 * WebAPI ACL Policy
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Policy;

class Acl extends \Magento\Authorization\Policy\Acl
{
    /**
     * @param \Magento\Acl\Builder $aclBuilder
     */
    public function __construct(\Magento\Acl\Builder $aclBuilder)
    {
        parent::__construct($aclBuilder);
    }
}
