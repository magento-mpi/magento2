<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento;

interface BaseScopeResolverInterface
{

    /**
     * Retrieve application scope object
     *
     * @param null|int $scopeId
     * @return \Magento\App\Config\ScopeInterface
     */
    public function getScope($scopeId = null);
}
