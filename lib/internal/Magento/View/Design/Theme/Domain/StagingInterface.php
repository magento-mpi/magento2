<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Domain;

/**
 * Interface StagingInterface
 */
interface StagingInterface
{
    /**
     * Copy changes from 'staging' theme
     *
     * @return \Magento\View\Design\Theme\Domain\StagingInterface
     */
    public function updateFromStagingTheme();
}