<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\V1\Entity\Eav;

use \Magento\Framework\Service\Data\Eav\AbstractObject;
use \Magento\Framework\Service\Data\Eav\MetadataObjectInterface;

/**
 * Class AttributeMetadata
 */
class AttributeMetadata extends AbstractObject implements MetadataObjectInterface
{
    /**#@+
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_ID = 'attribute_id';

    const ATTRIBUTE_CODE = 'attribute_code';
    /**#@-*/

    /**
     * Retrieve id of the attribute.
     *
     * @return string|null
     */
    public function getAttributeId()
    {
        return $this->_get(self::ATTRIBUTE_ID);
    }

    /**
     * Retrieve code of the attribute.
     *
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }
}
