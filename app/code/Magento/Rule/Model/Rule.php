<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Rule entity data model
 *
 * @deprecated since 1.7.0.0 use Magento_Rule_Model_Abstract instead
 *
 * @category Magento
 * @package Magento_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rule_Model_Rule extends Magento_Rule_Model_Abstract
{
    /**
     * @var Magento_Rule_Model_Condition_Combine
     */
    protected $_conditions;

    /**
     * @var Magento_Rule_Model_Action_Collection
     */
    protected $_actions;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Rule_Model_Condition_CombineFactory $conditionsFactory
     * @param Magento_Rule_Model_Action_CollectionFactory $actionsFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Rule_Model_Condition_CombineFactory $conditionsFactory,
        Magento_Rule_Model_Action_CollectionFactory $actionsFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_conditionsFactory = $conditionsFactory;
        $this->_actionsFactory = $actionsFactory;
        parent::__construct($formFactory, $context, $registry, $locale, $resource, $resourceCollection, $data);
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return Magento_Rule_Model_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return $this->_conditionsFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Magento_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionsFactory->create();
    }
}
