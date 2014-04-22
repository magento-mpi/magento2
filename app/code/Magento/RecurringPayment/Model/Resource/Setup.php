<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog entity setup
 *
 * @category    Magento
 * @package     Magento_Catalog
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
        $attributes = array(
            'is_recurring' => array(
                'type' => 'int',
                'label' => 'Enable Recurring Payment',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'note' => 'Products with recurring payment participate in catalog as nominal items.',
                'sort_order' => 1,
                'apply_to' => 'simple,virtual',
                'is_configurable' => false,
                'group' => 'Recurring Payment'
            ),
            'recurring_payment' => array(
                'type' => 'text',
                'label' => 'Recurring Payment',
                'input' => 'text',
                'backend' => 'Magento\RecurringPayment\Model\Product\Attribute\Backend\Recurring',
                'required' => false,
                'sort_order' => 2,
                'apply_to' => 'simple,virtual',
                'is_configurable' => false,
                'group' => 'Recurring Payment'
            )
        );
        foreach ($attributes as $attrCode => $attr) {
            $this->addAttribute('catalog_product', $attrCode, $attr);
        }
    }
}
