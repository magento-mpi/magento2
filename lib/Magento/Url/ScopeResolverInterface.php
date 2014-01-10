<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

interface ScopeResolverInterface {

    /**
     * Retrieve application scope object
     *
     * @param null|int $scopeId
     * @return \Magento\Url\ScopeInterface
     */
    public function getScope($scopeId = null);

    /**
     * Retrieve scopes array
     *
     * @return \Magento\Url\ScopeInterface[]
     */
    public function getScopes();
}
