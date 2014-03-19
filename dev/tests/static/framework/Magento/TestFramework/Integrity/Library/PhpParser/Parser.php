<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Integrity\Library\PhpParser;

/**
 * Parser for each token type
 *
 * @package Magento\TestFramework
 */
interface Parser
{
    /**
     * Parse specific token
     *
     * @param array|string $value
     * @param int $key
     */
    public function parse($value, $key);
}
