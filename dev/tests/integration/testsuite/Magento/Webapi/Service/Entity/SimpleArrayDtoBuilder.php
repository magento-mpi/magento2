<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class SimpleArrayDtoBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param int[] $ids
     */
    public function setIds(array $ids)
    {
        $this->_data['ids'] = $ids;
    }
}
