<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class AssociativeArrayDataObjectBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param string[] $associativeArray
     */
    public function setAssociativeArray(array $associativeArray)
    {
        $this->_data['associativeArray'] = $associativeArray;
    }
}
