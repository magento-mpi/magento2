<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\CatalogRule\Model\Rule\Action;

class Collection extends \Magento\Rule\Model\Action\Collection
{
    /**
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Rule\Model\ActionFactory $actionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Asset\Repository $assetRepo,
        \Magento\View\LayoutInterface $layout,
        \Magento\Rule\Model\ActionFactory $actionFactory,
        array $data = array()
    ) {
        parent::__construct($assetRepo, $layout, $actionFactory, $data);
        $this->setType('Magento\CatalogRule\Model\Rule\Action\Collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array(
                'value' => 'Magento\CatalogRule\Model\Rule\Action\Product',
                'label' => __('Update the Product')
        )));
        return $actions;
    }
}
