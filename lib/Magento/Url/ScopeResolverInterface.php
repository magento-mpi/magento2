<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Url;

interface ScopeResolverInterface extends \Magento\Framework\App\ScopeResolverInterface
{
    /**
     * Retrieve scopes array
     *
     * @return \Magento\Url\ScopeInterface[]
     */
    public function getScopes();

    /**
     * Retrieve area code
     *
     * @return \Magento\Url\ScopeInterface[]
     */
    public function getAreaCode();
}
