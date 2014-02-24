<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDtoBuilder;

class AssociativeArrayDtoBuilder extends AbstractDtoBuilder
{
    /**
     * @param string[] $associativeArray
     * @return $this
     */
    public function setAssociativeArray($associativeArray)
    {
        $this->_data['associativeArray'] = $associativeArray;
        return $this;
    }
}
