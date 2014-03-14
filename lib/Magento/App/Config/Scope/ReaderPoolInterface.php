<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\App\Config\Scope;

interface ReaderPoolInterface
{
    /**
     * Retrieve reader by scope
     *
     * @param string $scopeType
     * @return ReaderInterface|null
     */
    public function getReader($scopeType);
}
