<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

/**
 * Parser Interface
 */
interface ParserInterface
{
    /**
     * Parse files
     *
     * @param array $files
     */
    public function parse(array $files);
}
