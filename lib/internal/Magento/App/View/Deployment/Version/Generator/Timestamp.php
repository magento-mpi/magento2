<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\View\Deployment\Version\Generator;

/**
 * Generation of deployment version of static files using the timestamp
 */
class Timestamp implements \Magento\App\View\Deployment\Version\GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return (string)time();
    }
}
