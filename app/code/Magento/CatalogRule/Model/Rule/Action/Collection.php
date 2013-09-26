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
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Rule\Model\ActionFactory $actionFactory
     * @param \Magento\Core\Model\Layout $layout
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Rule\Model\ActionFactory $actionFactory,
        \Magento\Core\Model\Layout $layout,
        array $data = array()
    ) {
        parent::__construct($viewUrl, $actionFactory, $layout, $data);
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
