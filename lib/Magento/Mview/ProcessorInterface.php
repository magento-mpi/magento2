<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

interface ProcessorInterface
{
    /**
     * Materialize all views by group (all views if empty)
     *
     * @param string $group
     */
    public function update($group = '');

    /**
     * Clear all views' changelogs by group (all views if empty)
     *
     * @param string $group
     */
    public function clearChangelog($group = '');
}
