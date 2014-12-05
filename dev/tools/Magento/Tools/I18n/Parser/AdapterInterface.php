<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Parser;

/**
 * Adapter Interface
 */
interface AdapterInterface
{
    /**
     * Parse file
     *
     * @param string $file
     * @return array
     */
    public function parse($file);

    /**
     * Get parsed phrases
     *
     * @return array
     */
    public function getPhrases();
}
