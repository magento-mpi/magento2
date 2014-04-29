<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class NestedDataObjectBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDataObject $details
     */
    public function setDetails($details)
    {
        $this->_data['details'] = $details;
    }
}
