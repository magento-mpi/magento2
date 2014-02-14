<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View\State;

interface CollectionInterface
{
    /**
     * Retrieve loaded states
     *
     * @return array
     */
    public function getItems();
}
