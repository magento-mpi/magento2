<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

class AssociativeArrayDataBuilder extends AbstractExtensibleObjectBuilder
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
