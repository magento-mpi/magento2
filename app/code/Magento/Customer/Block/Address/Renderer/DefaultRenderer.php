<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Address format renderer default
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Address\Renderer;

use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Eav\Model\AttributeDataFactory;

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
    protected $_attrDataFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_metadataService;

    /**
     * @param \Magento\View\Element\Context $context
     * @param ElementFactory $attrDataFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        ElementFactory $attrDataFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService,
        array $data = array()
    ) {
        $this->_attrDataFactory = $attrDataFactory;
        $this->_countryFactory = $countryFactory;
        $this->_metadataService = $metadataService;
        parent::__construct($context, $data);
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
     * Get a format object for a given address, based on the type set earlier.
     *
     * @deprecated All new code should use formatArray based on Metadata service
     * @param string[] $addressAttributes
     * @return \Magento\Directory\Model\Country\Format
     */
    public function getFormat($addressAttributes = null)
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
     */
    public function render($addressAttributes, $format = null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_TEXT;
                break;
        }

        /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[] $attributesMetadata */
        $attributesMetadata = $this->_metadataService->getAllAddressAttributeMetadata();
        $data = array();
        /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attributeMetadata */
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            if ($attributeCode == 'country_id' && isset($addressAttributes['country_id'])) {
                $data['country'] = $this->_countryFactory->create(['id' => $addressAttributes['country_id']])->getName();
            } elseif ($attributeCode == 'region' && isset($addressAttributes['region'])) {
                $data['region'] = __($addressAttributes['region']);
            } elseif (isset($addressAttributes[$attributeCode])) {
                $value = $addressAttributes[$attributeCode];
                $dataModel = $this->_attrDataFactory->create($attributeMetadata, $value, 'customer_address');
                $value     = $dataModel->outputValue($dataFormat);
                if ($attributeMetadata->getFrontendInput() == 'multiline') {
                    $values    = $dataModel->outputValue(AttributeDataFactory::OUTPUT_FORMAT_ARRAY);
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
        $format = !is_null($format) ? $format : $this->getFormat($addressAttributes);
        return $this->filterManager->template($format, array('variables' => $data));
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
     */
    public function renderArray($addressAttributes, $format = null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = AttributeDataFactory::OUTPUT_FORMAT_TEXT;
                break;
        }

        /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[] $attributesMetadata */
        $attributesMetadata = $this->_metadataService->getAllAddressAttributeMetadata();
        $data = array();
        /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attributeMetadata */
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            if ($attributeCode == 'country_id' && isset($addressAttributes['country_id'])) {
                $data['country'] = $this->_countryFactory->create(['id' => $addressAttributes['country_id']])->getName();
            } elseif ($attributeCode == 'region' && isset($addressAttributes['region'])) {
                $data['region'] = __($addressAttributes['region']);
            } elseif (isset($addressAttributes[$attributeCode])) {
                $value = $addressAttributes[$attributeCode];
                $dataModel = $this->_attrDataFactory->create($attributeMetadata, $value, 'customer_address');
                $value = $dataModel->outputValue($dataFormat);
                if ($attributeMetadata->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(AttributeDataFactory::OUTPUT_FORMAT_ARRAY);
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
