<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

/**
 * Parser Interface
 */
interface ParserInterface
{
    /**
     * Parse path
     */
    public function parse();

    /**
     * Get parsed phrases
     *
     * @return array
     */
    public function getPhrases();
}
