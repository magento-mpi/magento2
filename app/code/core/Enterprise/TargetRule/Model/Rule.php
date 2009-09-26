<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_TargetRule_Model_Rule extends Mage_Rule_Model_Rule
{
    const BOTH_SELECTED_AND_RULE_BASED  = 0;
    const SELECTED_ONLY                 = 1;
    const RULE_BASED_ONLY               = 2;

    const RELATED_PRODUCTS              = 0;
    const UP_SELLS                      = 1;
    const CROSS_SELLS                   = 2;

    const XML_PATH_DEFAULT_VALUES       = 'catalog/enterprise_targetrule/';

    /**
     * Matched product ids array
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_targetrule/rule');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_TargetRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('enterprise_targetrule/rule_condition_combine');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_TargetRule_Model_Rule_Condition_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel('enterprise_targetrule/actions_condition_combine');
    }

    /**
     * Initialize rule model data from array
     *
     * @param   array $rule
     * @return  Enterprise_TargetRule_Model_Rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        return $this;
    }

    /**
     * Get options for `Apply to` field
     *
     * @return array
     */
    public function getAppliesToOptions()
    {
        return array(
            Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS
                => Mage::helper('enterprise_targetrule')->__('Related Products'),
            Enterprise_TargetRule_Model_Rule::UP_SELLS
                => Mage::helper('enterprise_targetrule')->__('Up-sells'),
            Enterprise_TargetRule_Model_Rule::CROSS_SELLS
                => Mage::helper('enterprise_targetrule')->__('Cross-sells'),
        );
    }

    /**
     * Retrieve Customer Segment Relations
     * Return empty array for rule didn't save or didn't use customer segment limitation
     *
     * @return array
     */
    public function getCustomerSegmentRelations()
    {
        if (!$this->getUseCustomerSegment() || !$this->getId()) {
            return array();
        }
        $relations = $this->_getData('customer_segment_relations');
        if (!is_array($relations)) {
            $relations = $this->_getResource()->getCustomerSegmentRelations($this->getId());
            $this->setData('customer_segment_relations', $relations);
        }

        return $relations;
    }

    /**
     * Set customer segment relations
     *
     * @param array|string $relations
     * @return Enterprise_TargetRule_Model_Rule
     */
    public function setCustomerSegmentRelations($relations)
    {
        if (is_array($relations)) {
            $this->setData('customer_segment_relations', $relations);
        } else if (is_string($relations)) {
            if (empty($relations)) {
                $relations = array();
            } else {
                $relations = explode(',', $relations);
            }
            $this->setData('customer_segment_relations', $relations);
        }

        return $this;
    }

    /**
     * Retrieve array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $productCollection = Mage::getResourceModel('catalog/product_collection');

            $this->setCollectedAttributes(array());
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->_productIds = array();
            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(
                    array($this, 'callbackValidateProduct')
                ),
                array(
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => Mage::getModel('catalog/product'),
                )
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     */
    public function callbackValidateProduct($args)
    {
        $product = $args['product']->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
}
