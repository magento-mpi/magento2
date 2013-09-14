<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model\Rule\Action;

class Collection extends \Magento\Rule\Model\Action\Collection
{
    /**
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param array $data
     */
    public function __construct(\Magento\Core\Model\View\Url $viewUrl, array $data = array())
    {
        parent::__construct($viewUrl, $data);
        $this->setType('Magento\SalesRule\Model\Rule\Action\Collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(array(
            'value' => 'Magento\SalesRule\Model\Rule\Action\Product',
            'label' => __('Update the Product'))
        ));
        return $actions;
    }
}
