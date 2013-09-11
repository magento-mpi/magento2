<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Shell;

interface CommandInterface
{
    /**
     * Execute command
     *
     * @return string
     */
    public function execute();
}
