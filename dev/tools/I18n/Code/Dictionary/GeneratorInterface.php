<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

/**
 * Generator interface
 */
interface GeneratorInterface
{
    /**
     * Generate dictionary
     *
     * @param bool $withContext
     */
    public function generate($withContext = true);
}
