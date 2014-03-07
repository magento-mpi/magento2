<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Data\AbstractObjectBuilder;

class NestedDataBuilder extends AbstractObjectBuilder
{
    /**
     * @param string $details
     * @return $this
     */
    public function setDetails($details)
    {
        $this->_data['details'] = $details;
        return $this;
    }
}
