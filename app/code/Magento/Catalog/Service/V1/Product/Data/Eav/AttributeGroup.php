<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Data\Eav;


class AttributeGroup extends \Magento\Framework\Service\Data\AbstractObject
{
    const KEY_ID = 'id';

    const KEY_NAME = 'name';

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }
} 
