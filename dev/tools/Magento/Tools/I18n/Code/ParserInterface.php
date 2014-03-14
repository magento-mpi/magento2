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
     * Parse by parser options
     *
     * @param array $parseOptions
     * @return array
     */
    public function parse(array $parseOptions);

    /**
     * Get parsed phrases
     *
     * @return array
     */
    public function getPhrases();
}
