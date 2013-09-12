<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler\Log\Writer;

class Quiet implements WriterInterface
{
    /**
     * Output log data
     *
     * @param array $data
     */
    public function write(array $data)
    {
        // Do nothing
    }
}
