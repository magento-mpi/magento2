<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Translate\Inline;

interface ProviderInterface
{
    /**
     * Return instance of inline translate class
     *
     * @return \Magento\Translate\InlineInterface
     */
    public function get();
}
