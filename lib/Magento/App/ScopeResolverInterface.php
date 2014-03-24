<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

interface ScopeResolverInterface
{

    /**
     * Retrieve application scope object
     *
     * @param null|int $scopeId
     * @return \Magento\App\ScopeInterface
     */
    public function getScope($scopeId = null);
}
