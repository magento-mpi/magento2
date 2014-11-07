<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class NestedBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDataObject $details
     */
    public function setDetails($details)
    {
        $this->data['details'] = $details;
    }
}
