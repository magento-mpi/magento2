<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer;

/**
 * Interface CreatorInterface
 */
interface CreatorInterface
{

    /**
     * Creates composer.json files for components
     * @return int
     */
    public function create();
}