<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\CodingStandard\Tool;

interface ExtensionInterface
{
    /**
     * Set extensions for tool to run
     * Example: 'php', 'xml', 'phtml', 'css'
     *
     * @param array $extensions
     * @return void
     */
    public function setExtensions(array $extensions);
}
