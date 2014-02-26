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
 * @deprecated since 1.7.0.0 use \Magento\Rule\Model\AbstractModel instead
 *
 * @category Magento
 * @package Magento_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rule\Model;

class Rule extends AbstractModel
{
    /**
     * @var \Magento\Rule\Model\Condition\Combine
     */
    protected $_conditions;

    /**
     * @var \Magento\Rule\Model\Action\Collection
     */
    protected $_actions;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Rule\Model\Condition\CombineFactory $conditionsFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $actionsFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Rule\Model\Condition\CombineFactory $conditionsFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionsFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_conditionsFactory = $conditionsFactory;
        $this->_actionsFactory = $actionsFactory;
        parent::__construct($context, $registry, $formFactory, $locale, $resource, $resourceCollection, $data);
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_conditionsFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionsFactory->create();
    }
}
