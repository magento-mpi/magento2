<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Search;

use Magento\Service\Entity\AbstractDto;

/**
 * Groups two or more filters together using logical OR.
 */
class OrGroup extends AbstractFilterGroup
{
    /**
     * {@inheritdoc}
     */
    public function getGroupType()
    {
        return 'OR';
    }
}
