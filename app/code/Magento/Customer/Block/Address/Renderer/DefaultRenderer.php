<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Address\Renderer;

use Magento\Customer\Model\Metadata\ElementFactory;

/**
 * Address format renderer default
 */
class DefaultRenderer
    extends \Magento\View\Element\AbstractBlock
    implements \Magento\Customer\Block\Address\Renderer\RendererInterface
{
    /**
     * Format type object
     *
     * @var \Magento\Object
     */
    protected $_type;

    /**
     * @var ElementFactory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    protected $_metadataService;

    /**
     * Address converter
     *
     * @var \Magento\Customer\Model\Address\Converter
     */
    protected $_addressConverter;

    /**
     * Constructor
     *
     * @param \Magento\View\Element\Context $context
     * @param ElementFactory $elementFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory ,
     * @param \Magento\Customer\Model\Address\Converter $addressConverter
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        ElementFactory $elementFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Model\Address\Converter $addressConverter,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_addressConverter = $addressConverter;
        $this->_countryFactory = $countryFactory;
        $this->_metadataService = $metadataService;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve format type object
     *
     * @return \Magento\Object
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Retrieve format type object
     *
     * @param  \Magento\Object $type
     * @return \Magento\Customer\Block\Address\Renderer\DefaultRenderer
     */
    public function setType(\Magento\Object $type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @deprecated All new code should use renderArray based on Metadata service
     * @param \Magento\Customer\Model\Address\AbstractAddress $address
     * @return string
     */
    public function getFormat(\Magento\Customer\Model\Address\AbstractAddress $address = null)
    {
        $countryFormat = is_null($address)
            ? false
            : $address->getCountryModel()->getFormat($this->getType()->getCode());
        $format = $countryFormat ? $countryFormat->getFormat() : $this->getType()->getDefaultFormat();
        return $format;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Customer\Model\Address\AbstractAddress $address, $format = null)
    {
        $attributes = $this->_addressConverter->createAddressFromModel($address, 0, 0)
            ->getAttributes();
        return $this->renderArray($attributes, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatArray($addressAttributes = null)
    {
        $countryFormat = false;
        if ($addressAttributes && isset($addressAttributes['country_id'])) {
            /** @var \Magento\Directory\Model\Country $country */
            $country = $this->_countryFactory->create()->load($addressAttributes['country_id']);
            $countryFormat = $country->getFormat($this->getType()->getCode());
        }
        $format = $countryFormat ? $countryFormat->getFormat() : $this->getType()->getDefaultFormat();
        return $format;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function renderArray($addressAttributes, $format = null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = ElementFactory::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = ElementFactory::OUTPUT_FORMAT_TEXT;
                break;
        }

        $attributesMetadata = $this->_metadataService->getAllAddressAttributeMetadata();
        $data = array();
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            if ($attributeCode == 'country_id' && isset($addressAttributes['country_id'])) {
                $data['country'] = $this->_countryFactory->create()
                    ->loadByCode($addressAttributes['country_id'])
                    ->getName();
            } elseif ($attributeCode == 'region' && isset($addressAttributes['region'])) {
                $data['region'] = __($addressAttributes['region']);
            } elseif (isset($addressAttributes[$attributeCode])) {
                $value = $addressAttributes[$attributeCode];
                $dataModel = $this->_elementFactory->create($attributeMetadata, $value, 'customer_address');
                $value = $dataModel->outputValue($dataFormat);
                if ($attributeMetadata->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(ElementFactory::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attributeCode, $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attributeCode] = $value;
            }
        }
        if ($this->getType()->getEscapeHtml()) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeHtml($value);
            }
        }
        $format = !is_null($format) ? $format : $this->getFormatArray($addressAttributes);
        return $this->filterManager->template($format, array('variables' => $data));
    }
}
