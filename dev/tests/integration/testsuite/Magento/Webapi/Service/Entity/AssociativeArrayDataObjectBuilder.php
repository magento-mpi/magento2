<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class AssociativeArrayDataObjectBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string[] $associativeArray
     */
    public function setAssociativeArray(array $associativeArray)
    {
        $this->_data['associativeArray'] = $associativeArray;
    }
}
