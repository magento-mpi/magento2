<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDtoBuilder;

class SimpleArrayDtoBuilder extends AbstractDtoBuilder
{
    /**
     * @param array $ids
     * @return $this
     */
    public function setIds($ids)
    {
        $this->_data['ids'] = $ids;
        return $this;
    }
}
