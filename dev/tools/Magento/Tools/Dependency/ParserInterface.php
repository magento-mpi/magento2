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
     * @param array $options
     * @return array
     */
    public function parse(array $options);
}
