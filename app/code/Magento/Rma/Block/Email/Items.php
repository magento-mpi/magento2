<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Email;

class Items extends \Magento\Rma\Block\Form
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Rma eav
     *
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_rmaEav = null;

    /**
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Rma\Helper\Eav $rmaEav
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Rma\Helper\Eav $rmaEav,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_rmaEav = $rmaEav;
        parent::__construct($modelFactory, $formFactory, $eavConfig, $coreData, $context, $data);
    }

    /**
     * Get string label of option-type item attributes
     *
     * @param int $attributeValue
     * @return string
     */
    public function getOptionAttributeStringValue($attributeValue)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = $this->_rmaEav->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$attributeValue])) {
            return $this->_attributeOptionValues[$attributeValue];
        } else {
            return '';
        }
    }
}
