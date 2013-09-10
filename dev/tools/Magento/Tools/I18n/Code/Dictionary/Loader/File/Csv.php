<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Loader\File;

use Magento\Tools\I18n\Code\Dictionary;

/**
 *  Dictionary loader from csv
 */
class Csv extends AbstractFile
{
    /**
     * {@inheritdoc}
     */
    protected function _readFile()
    {
        return fgetcsv($this->_fileHandler, null, ',', '"');
    }
}
