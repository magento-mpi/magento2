<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Writer\Csv;

use Magento\Tools\I18n\Code\Dictionary\Writer\Csv as BaseCsv;

/**
 * Stdout writer
 *
 * Output csv format to stdout
 */
class Stdo extends BaseCsv
{
    /**
     * Writer construct
     */
    public function __construct()
    {
        $this->_fileHandler = STDOUT;
    }
}
