<?php
/**
 * Google AdWords Conversion Abstract Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\GoogleAdwords\Model\Config\Backend;

abstract class ConversionAbstract extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Validator\Composite\VarienObject
     */
    protected $_validatorComposite;

    /**
     * @var \Magento\GoogleAdwords\Model\Validator\Factory
     */
    protected $_validatorFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory
     * @param \Magento\GoogleAdwords\Model\Validator\Factory $validatorFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory,
        \Magento\GoogleAdwords\Model\Validator\Factory $validatorFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null
    ) {
        parent::__construct($context, $resource, $resourceCollection);

        $this->_validatorFactory = $validatorFactory;
        $this->_validatorComposite = $validatorCompositeFactory->create();
    }
}
