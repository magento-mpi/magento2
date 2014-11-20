<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\ExtensibleObjectBuilder;

class AssociativeArrayBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param string[] $associativeArray
     * @return $this
     */
    public function setAssociativeArray($associativeArray)
    {
        $this->data['associativeArray'] = $associativeArray;
        return $this;
    }
}
