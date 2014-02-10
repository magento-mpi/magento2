<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Writer;

use Magento\Tools\Dependency\Report\WriterInterface;

/**
 * Csv file writer
 */
class Csv implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(array $data, $filename)
    {
        // TODO: MAGETWO-20687
    }
}
