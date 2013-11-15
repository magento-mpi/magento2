<?php
/**
 * Application area front name resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Area;

interface FrontNameResolverInterface
{
    /**
     * Retrieve front name
     *
     * @return string
     */
    public function getFrontName();
}