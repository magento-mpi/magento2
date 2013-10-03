<?php
/**
 * Used to get class information associated with an alias, and stored in config files.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService;

interface ConfigInterface
{
    /**
     * Get the class information for a given service call
     *
     * @param $alias
     * @return mixed
     */
    public function getClassByAlias($alias);
}
