<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml weee tax item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Weee\Block\Renderer\Weee;

class Tax
    extends \Magento\Backend\Block\Widget
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    protected $_element = null;
    protected $_countries = null;
    protected $_websites = null;
    protected $_template = 'renderer/tax.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_sourceCountry;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $sourceCountry
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $sourceCountry,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_sourceCountry = $sourceCountry;
        $this->_directoryHelper = $directoryHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Tax'),
                'data_attribute' => array('action' => 'add-fpt-item'),
                'class' => 'add'
            )
        );
        $this->addChild(
            'delete_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Delete Tax'),
                'data_attribute' => array('action' => 'delete-fpt-item'),
                'class' => 'delete'
            )
        );
        return parent::_prepareLayout();
    }

    public function setElement(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data) && count($data)) {
            usort($data, array($this, '_sortWeeeTaxes'));
            $values = $data;
        }
        return $values;
    }

    protected function _sortWeeeTaxes($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['country'] != $b['country']) {
            return $a['country'] < $b['country'] ? -1 : 1;
        }
        return 0;
    }

    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    public function isMultiWebsites()
    {
        return !$this->_storeManager->hasSingleStore();
    }

    public function getCountries()
    {
        if (is_null($this->_countries)) {
            $this->_countries = $this->_sourceCountry->toOptionArray();
        }

        return $this->_countries;
    }

    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'     => __('All Websites'),
            'currency' => $this->_directoryHelper->getBaseCurrencyCode()
        );

        if (!$this->_storeManager->hasSingleStore() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            if ($storeId = $this->getProduct()->getStoreId()) {
                $website = $this->_storeManager->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name'     => $website->getName(),
                    'currency' => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach ($this->_storeManager->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name'     => $website->getName(),
                        'currency' => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}
