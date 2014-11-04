<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\ExtensibleObjectBuilder;

class SimpleArrayBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param array $ids
     * @return $this
     */
    public function setIds($ids)
    {
        $this->data['ids'] = $ids;
        return $this;
    }
}
