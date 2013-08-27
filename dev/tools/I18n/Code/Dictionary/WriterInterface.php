<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

/**
 * Writer interface
 */
interface WriterInterface
{
    /**
     * Write data to dictionary
     *
     * @param array $data
     */
    public function write(array $data);
}
