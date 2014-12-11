<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogImportExport\Model\Export\Product\Type;

/**
 * Export entity product type simple model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Simple extends \Magento\CatalogImportExport\Model\Export\Product\Type\AbstractType
{
    /**
     * Overridden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'has_options' => array('source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'),
        'required_options' => array('source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'),
        'created_at' => array('backend_type' => 'datetime'),
        'updated_at' => array('backend_type' => 'datetime')
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var string[]
     */
    protected $_disabledAttrs = array(
        'old_id',
        'tier_price',
        'group_price',
        'category_ids'
    );
}
