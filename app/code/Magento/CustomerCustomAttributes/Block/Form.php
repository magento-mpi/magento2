<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Block;

/**
 * Customer Dynamic attributes Form Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\CustomAttributeManagement\Block\Form
{
    /**
     * @var \Magento\Customer\Model\Metadata\Form
     */
    protected $_metadataForm;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
    }

    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Magento\Customer\Model\Form';

    /**
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function getMetadataForm()
    {
        if (is_null($this->_metadataForm)) {
            $this->_metadataForm = $this->_metadataFormFactory->create(
                $this->_entityType->getEntityTypeCode(),
                $this->_formCode
            );
            // @todo initialize default values  MAGETWO-17600
        }
        return $this->_metadataForm;
    }

    /**
     * Return whether the form should be opened in an expanded mode showing the change password fields
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getChangePassword()
    {
        return $this->_customerSession->getChangePassword();
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getEntity()
    {
        if (is_null($this->_entity) && $this->_entityModelClass) {
            $this->_entity = $this->_modelFactory->create($this->_entityModelClass);
            $entityId = $this->_customerSession->getCustomerId();
            if ($entityId) {
                $this->_entity->load($entityId);
            }
        }
        return $this->_entity;
    }
}
