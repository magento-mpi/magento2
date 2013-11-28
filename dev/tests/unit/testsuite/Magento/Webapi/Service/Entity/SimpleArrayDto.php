<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

/**
 * Class SimpleArrayDto
 *
 * @package Magento\Webapi\Service\Entity
 */
class SimpleArrayDto extends AbstractDto
{
    /**
     * @return array
     */
    public function getIds()
    {
        return $this->_getData('ids');
    }

    /**
     * @param array $ids
     *
     * @return SimpleArrayDto
     */
    public function setIds(array $ids)
    {
        return $this->_setData('ids', $ids);
    }
}