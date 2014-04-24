<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class AssociativeArrayDataBuilder extends AbstractObjectBuilder
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
