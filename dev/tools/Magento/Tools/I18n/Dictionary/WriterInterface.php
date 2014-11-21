<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Dictionary;

/**
 * Writer interface
 */
interface WriterInterface
{
    /**
     * Write data to dictionary
     *
     * @param Phrase $phrase
     * @return void
     */
    public function write(Phrase $phrase);
}
