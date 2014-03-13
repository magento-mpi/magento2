<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class SimpleArrayDataObjectBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param int[] $ids
     */
    public function setIds(array $ids)
    {
        $this->_data['ids'] = $ids;
    }
}
