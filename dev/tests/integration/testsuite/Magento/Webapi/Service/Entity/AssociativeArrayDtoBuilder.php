<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class AssociativeArrayDtoBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param string[] $associativeArray
     */
    public function setAssociativeArray(array $associativeArray)
    {
        $this->_data['associativeArray'] = $associativeArray;
    }
}
