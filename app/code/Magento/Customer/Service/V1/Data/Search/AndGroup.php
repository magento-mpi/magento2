<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Service\Entity\AbstractObject;

/**
 * Groups two or more filters together using logical AND.
 */
class AndGroup extends AbstractFilterGroup
{
    /**
     * {@inheritdoc}
     */
    public function getGroupType()
    {
        return 'AND';
    }
}
