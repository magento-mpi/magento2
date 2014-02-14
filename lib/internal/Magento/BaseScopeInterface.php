<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

interface BaseScopeInterface
{
    /**
     * Default scope type
     */
    const SCOPE_DEFAULT = 'default';

    /**
     * Retrieve scope code
     *
     * @return string
     */
    public function getCode();
}
