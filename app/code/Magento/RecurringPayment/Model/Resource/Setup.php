<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog entity setup
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringPayment\Model\Resource;

class Setup extends \Magento\Catalog\Model\Resource\Setup
{
    /**
     * Default entites and attributes
     *
     * @param array|null $entities
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function installEntities($entities = null)
    {
        $attributes = [
            'is_recurring' => [
                'type' => 'int',
                'label' => 'Enable Recurring Payment',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'note' => 'Products with recurring payment participate in catalog as nominal items.',
                'sort_order' => 1,
                'apply_to' => 'simple,virtual',
                'is_configurable' => false,
                'group' => 'Recurring Payment',
            ],
            'recurring_payment' => [
                'type' => 'text',
                'label' => 'Recurring Payment',
                'input' => 'text',
                'backend' => 'Magento\RecurringPayment\Model\Product\Attribute\Backend\Recurring',
                'required' => false,
                'sort_order' => 2,
                'apply_to' => 'simple,virtual',
                'is_configurable' => false,
                'group' => 'Recurring Payment',
            ],
        ];
        foreach ($attributes as $attrCode => $attr) {
            $this->addAttribute('catalog_product', $attrCode, $attr);
        }
    }
}
