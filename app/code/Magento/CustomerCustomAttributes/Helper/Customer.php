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
 * Enterprise Customer EAV Attributes Data Helper
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
namespace Magento\CustomerCustomAttributes\Helper;

class Customer extends \Magento\CustomAttribute\Helper\Data
{
    /**
     * Data helper
     *
     * @var \Magento\CustomerCustomAttributes\Helper\Data $_dataHelper
     */
    protected $_dataHelper;

    /**
     * Input validator
     *
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $_inputValidator
     */
    protected $_inputValidator;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\CustomerCustomAttributes\Helper\Data $dataHelper
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Helper\Context $context,
        \Magento\CustomerCustomAttributes\Helper\Data $dataHelper,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
    ) {
        parent::__construct($eavConfig, $locale, $context);
        $this->_dataHelper = $dataHelper;
        $this->_inputValidator = $inputValidator;
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => __('Customer Checkout Register'),
                'value' => 'checkout_register'
            ),
            array(
                'label' => __('Customer Registration'),
                'value' => 'customer_account_create'
            ),
            array(
                'label' => __('Customer Account Edit'),
                'value' => 'customer_account_edit'
            ),
            array(
                'label' => __('Admin Checkout'),
                'value' => 'adminhtml_checkout'
            ),
        );
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @throws \Magento\Core\Exception
     * @return array
     */
    public function filterPostData($data)
    {
        $data = parent::filterPostData($data);

        //validate frontend_input
        if (isset($data['frontend_input'])) {
            $this->_inputValidator->setHaystack(
                array_keys($this->_dataHelper->getAttributeInputTypes())
            );
            if (!$this->_inputValidator->isValid($data['frontend_input'])) {
                throw new \Magento\Core\Exception($this->stripTags(implode(' ', $this->_inputValidator->getMessages())));
            }
        }
        return $data;
    }
}
