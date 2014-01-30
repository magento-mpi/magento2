<?php
/**
 * Inline Translation config interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

interface ConfigInterface
{
    /**
     * Check whether inline translation is enabled
     *
     * @param int|null $store
     * @return bool
     */
    public function isActive($store = null);


    /**
     * Check whether allowed client ip for inline translation
     *
     * @param mixed $store
     * @return bool
     */
    public function isDevAllowed($store = null);
}
