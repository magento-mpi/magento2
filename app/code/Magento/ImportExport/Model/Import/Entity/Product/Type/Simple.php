<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import\Entity\Product\Type;

/**
 * Import entity simple product type
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Simple extends \Magento\ImportExport\Model\Import\Entity\Product\Type\AbstractType
{
    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility property.
     *
     * @var string[]
     */
    protected $_forcedAttributesCodes = array(
        'related_tgtr_position_behavior',
        'related_tgtr_position_limit',
        'upsell_tgtr_position_behavior',
        'upsell_tgtr_position_limit'
    );
}
