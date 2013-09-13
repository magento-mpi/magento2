<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

use \Magento\Tools\I18n\Code\Dictionary\Phrase;

/**
 * Writer interface
 */
interface WriterInterface
{
    /**
     * Write data to dictionary
     *
     * @param \Magento\Tools\I18n\Code\Dictionary\Phrase $phrase
     */
    public function write(Phrase $phrase);
}
