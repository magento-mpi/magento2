<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Source\Import\Behavior;

/**
 * Import behavior source model used for defining the behaviour during the import.
 */
class Basic extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND => __('Append Complex Data'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE => __('Replace Existing Complex Data'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE => __('Delete Entities')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'basic';
    }
}
