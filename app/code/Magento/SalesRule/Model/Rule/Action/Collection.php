<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Action_Collection extends Magento_Rule_Model_Action_Collection
{
    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(Magento_Core_Model_View_Url $viewUrl, array $data = array())
    {
        parent::__construct($viewUrl, $data);
        $this->setType('Magento_SalesRule_Model_Rule_Action_Collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(array(
            'value' => 'Magento_SalesRule_Model_Rule_Action_Product',
            'label' => __('Update the Product'))
        ));
        return $actions;
    }
}
