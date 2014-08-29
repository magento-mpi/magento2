<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

class NestedDataObjectRequestBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param \Magento\TestModule4\Service\V1\Entity\DataObjectRequest $details
     * @return \Magento\TestModule4\Service\V1\Entity\DataObjectRequest
     */
    public function setDetails(DataObjectRequest $details)
    {
        return $this->_set('details', $details);
    }
}
