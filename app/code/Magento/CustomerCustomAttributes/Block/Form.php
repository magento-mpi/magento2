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
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = array()
    ) {
        parent::__construct($context, $metadataFormFactory, $modelFactory, $formFactory, $eavConfig, $data);
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

}
