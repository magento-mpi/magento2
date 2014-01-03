<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

class SimpleArrayDto extends AbstractDto
{
    /**
     * @return array
     */
    public function getIds()
    {
        return $this->_get('ids');
    }

    /**
     * @param array $ids
     * @return SimpleArrayDto
     */
    public function setIds(array $ids)
    {
        return $this->_set('ids', $ids);
    }
}
