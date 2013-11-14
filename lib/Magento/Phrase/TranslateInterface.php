<?php
/**
 * Phrase translate interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Phrase;

interface TranslateInterface
{
    /**
     * @param array $args
     * @return string
     */
    public function translate($args);
}