<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Domain;

interface StagingInterface
{
    /**
     * Copy changes from 'staging' theme
     *
     * @return \Magento\Core\Model\Theme\Domain\Virtual
     */
    public function updateFromStagingTheme();
}