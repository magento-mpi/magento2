<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

/**
 * Emtity attribute option model
 *
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Option _getResource()
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Option getResource()
 * @method int getAttributeId()
 * @method \Magento\Eav\Model\Entity\Attribute\Option setAttributeId(int $value)
 * @method int getSortOrder()
 * @method \Magento\Eav\Model\Entity\Attribute\Option setSortOrder(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Eav\Model\Resource\Entity\Attribute\Option');
    }
}
