<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

class AssociativeArrayDto extends AbstractDto
{
    /**
     * @return string[]
     */
    public function getAssociativeArray()
    {
        return $this->_get('associativeArray');
    }

    /**
     * @param string[] $associativeArray
     * @return AssociativeArrayDto
     */
    public function setAssociativeArray(array $associativeArray)
    {
        return $this->_set('associativeArray', $associativeArray);
    }
}
