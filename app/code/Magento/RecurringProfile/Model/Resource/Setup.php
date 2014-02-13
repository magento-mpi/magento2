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
namespace Magento\RecurringProfile\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    /**
     * Default entites and attributes
     *
     * @return array
     */
    public function installEntities()
    {
        $attributes = [
            'is_recurring'       => [
                'type'                       => 'int',
                'label'                      => 'Enable Recurring Profile',
                'input'                      => 'select',
                'source'                     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'                   => false,
                'note'                       =>
                    'Products with recurring profile participate in catalog as nominal items.',
                'sort_order'                 => 1,
                'apply_to'                   => 'simple,virtual',
                'is_configurable'            => false,
                'group'                      => 'Recurring Profile',
            ],
            'recurring_profile'  => [
                'type'                       => 'text',
                'label'                      => 'Recurring Payment Profile',
                'input'                      => 'text',
                'backend'                    => 'Magento\RecurringProfile\Model\Product\Attribute\Backend\Recurring',
                'required'                   => false,
                'sort_order'                 => 2,
                'apply_to'                   => 'simple,virtual',
                'is_configurable'            => false,
                'group'                      => 'Recurring Profile',
            ]
        ];
        foreach ($attributes as $attrCode => $attr) {
            $this->addAttribute('catalog_product', $attrCode, $attr);
        }
    }
}
