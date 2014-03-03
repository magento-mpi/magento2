<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\View\Deployment\Version;

/**
 * Algorithm of generation of deployment version of static files
 */
interface GeneratorInterface
{
    /**
     * Return deployment version of static files that is unique enough for the current deployment
     *
     * @return string
     */
    public function generate();
}
