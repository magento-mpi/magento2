<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Publication\Edition;

/**
 * Edition configurator interface
 */
interface ConfiguratorInterface
{
    /**
     * Configure Magento instance
     *
     * @return void
     */
    public function configure();
}
