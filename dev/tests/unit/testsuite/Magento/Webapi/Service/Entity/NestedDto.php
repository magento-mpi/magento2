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
 * Class NestedDto
 *
 * @package Magento\Webapi\Service\Entity
 */
class NestedDto extends AbstractDto
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleDto
     */
    public function getDetails()
    {
        return $this->_get('details');
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto $details
     *
     * @return NestedDto
     */
    public function setDetails(SimpleDto $details)
    {
        return $this->_set('details', $details);
    }
}