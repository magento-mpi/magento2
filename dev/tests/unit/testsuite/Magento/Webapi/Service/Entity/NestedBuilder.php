<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\ExtensibleObjectBuilder;

class NestedBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param string $details
     * @return $this
     */
    public function setDetails($details)
    {
        $this->data['details'] = $details;
        return $this;
    }
}
