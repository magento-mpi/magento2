<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Rule\Action;

class Collection extends \Magento\Rule\Model\Action\Collection
{
    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Rule\Model\ActionFactory $actionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\LayoutInterface $layout,
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
        $actions = array_merge_recursive(
            $actions,
            array(
                array('value' => 'Magento\CatalogRule\Model\Rule\Action\Product', 'label' => __('Update the Product'))
            )
        );
        return $actions;
    }
}
