<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractObject;

class SimpleArrayData extends AbstractObject
{
    /**
     * @return int[]
     */
    public function getIds()
    {
        return $this->_get('ids');
    }
}
