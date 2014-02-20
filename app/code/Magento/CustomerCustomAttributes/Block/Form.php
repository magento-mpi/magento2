<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Dynamic attributes Form Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block;

class Form extends \Magento\CustomAttribute\Block\Form
{
    /** @var \Magento\Customer\Model\Metadata\Form */
    protected $_metadataForm;

    /** @var \Magento\Customer\Model\Metadata\FormFactory  */
    protected $_metadataFormFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        array $data = array()
    ) {
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
        $this->_metadataFormFactory = $metadataFormFactory;
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

    public function getChangePassword()
    {
        return $this->_customerSession->getChangePassword();
    }
}
