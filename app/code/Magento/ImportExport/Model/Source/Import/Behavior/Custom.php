<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Source\Import\Behavior;

/**
 * Import behavior source model used for customers' components import entities.
 * Source model used in new import entities in Magento 2.0.
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Custom
    extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE
                => __('Add/Update Complex Data'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE
                => __('Delete Entities'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_CUSTOM
                => __('Custom Action'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'custom';
    }
}
