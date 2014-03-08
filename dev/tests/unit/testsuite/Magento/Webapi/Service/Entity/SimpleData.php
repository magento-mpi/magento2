<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Data\AbstractObject;

class SimpleData extends AbstractObject
{
    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->_get('entityId');
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->_get('name');
    }
}
