<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer giftregistry list block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
namespace Magento\GiftRegistry\Block\Customer\Edit;

abstract class AbstractEdit extends \Magento\Directory\Block\Data
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\GiftRegistry\Model\Attribute\Config
     */
    protected $attributeConfig;

    /**
     * Registry Entity object
     *
     * @var \Magento\GiftRegistry\Model\Entity
     */
    protected $_entity = null;

    /**
     * Attribute groups array
     *
     * @var array
     */
    protected $_groups = null;

    /**
     * Static types fields holder
     *
     * @var array
     */
    protected $_staticTypes = array();

    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $locale;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        parent::__construct(
            $configCacheType, $coreData, $context, $storeManager,
            $regionCollFactory, $countryCollFactory, $data
        );

        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->attributeConfig = $attributeConfig;
        $this->storeManager = $storeManager;
        $this->locale = $locale;
    }

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_storeConfig->getConfig($path);
    }

    /**
     * Getter, return entity object , instantiated in controller
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('magento_giftregistry_entity');
    }

    /**
     * Getter for CustomAttributes Array
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->getEntity()->getCustomAttributes();
    }

    /**
     * Check if attribute is required
     *
     * @param array $data
     * @return bool
     */
    public function isAttributeRequired($data)
    {
        if (isset($data['frontend']) && is_array($data['frontend']) && !empty($data['frontend']['is_required'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if attribute needs region updater js object
     *
     * @param array $data
     * @return bool
     */
    public function useRegionUpdater($data)
    {
        return ($data['type'] == 'country' && !empty($data['show_region']));
    }

    /**
     * Check if attribute is static
     *
     * @param string $code
     * @return bool
     */
    public function isAttributeStatic($code)
    {
        $types = $this->attributeConfig->getStaticTypesCodes();
        if (in_array($code, $types)) {
            return true;
        }
        return false;
    }

    /**
     * Return array of attribute groups for using as options
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        return $this->attributeConfig->getAttributeGroups();
    }

    /**
     * Return group label
     *
     * @param string $groupId
     * @return string
     */
    public function getGroupLabel($groupId)
    {
        if ($this->_groups === null) {
            $this->_groups = $this->attributeConfig->getAttributeGroups();
        }
        if (is_array($this->_groups) && (!empty($this->_groups[$groupId]))
            && is_array($this->_groups[$groupId]) && !empty($this->_groups[$groupId]['label'])) {
            $label = $this->_groups[$groupId]['label'];
        } else {
            $label = $groupId;
        }
        return $label;
    }

    /**
     * JS Calendar html
     *
     * @param string $name   - DOM name
     * @param string $id     - DOM id
     * @param string $value
     * @param string $formatType  - full|long|medium|short
     * @param string $class
     *
     * @return string
     */
    public function getCalendarDateHtml($name, $id, $value, $formatType = false, $class = '')
    {
        if ($formatType === false) {
            $formatType = \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM;
        }

        $calendar = $this->getLayout()
            ->createBlock('Magento\GiftRegistry\Block\Customer\Date')
            ->setId($id)
            ->setName($name)
            ->setValue($this->formatDate($value, $formatType))
            ->setClass($class . ' product-custom-option datetime-picker input-text validate-date')
            ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
            ->setDateFormat($this->locale->getDateFormat($formatType));
        return $calendar->getHtml();
    }

    /**
     * Select element for choosing attribute group
     *
     * @return string
     */
    public function getSelectHtml($options, $name, $id, $value = false, $class = '')
    {
        $select = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
            ->setData(array(
                'id'    => $id,
                'class' => 'select global-scope '. $class
            ))
            ->setName($name)
            ->setValue($value)
            ->setOptions($options);
        return $select->getHtml();
    }

    /**
     * Reorder attributes array by group
     *
     * @param array $attributes
     * @return array
     */
    protected function _groupAttributes($attributes)
    {
        $grouped = array();
        if (is_array($attributes)) {
            foreach ($attributes as $field => $fdata){
                if (is_array($fdata)) {
                    $grouped[$fdata['group']][$field] = $fdata;
                    $grouped[$fdata['group']][$field]['id'] = $this->_getElementId($field);
                    $grouped[$fdata['group']][$field]['name'] = $this->_getElementName($field);

                    if ($fdata['type'] == 'country' && !empty($fdata['show_region'])) {
                        $regionCode = $field . '_region';
                        $regionAttribute['label'] = __('State/Province');
                        $regionAttribute['group'] = $fdata['group'];
                        $regionAttribute['type'] = 'region';
                        $regionAttribute['id'] = $this->_getElementId($regionCode);
                        $regionAttribute['name'] = $this->_getElementName($regionCode);
                        $grouped[$fdata['group']][$regionCode] = $regionAttribute;
                    }
                }
            }
        }
        return $grouped;
    }

    /**
     * Prepare html element name
     *
     * @param string $code
     * @return string
     */
    protected function _getElementName($code)
    {
        if (!$this->isAttributeStatic($code)) {
            return $this->_prefix . '[' . $code . ']';
        }
        return $code;
    }

    /**
     * Prepare html element id
     *
     * @param string $code
     * @return string
     */
    protected function _getElementId($code)
    {
        return $code;
    }

    /**
     * Get current type Id
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->getEntity()->getTypeId();
    }

    /**
     * Get current type label
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->getEntity()->getTypeLabel();
    }

    /**
     * Reorder data in group array for internal use
     *
     * @param array $selectOptions
     * @return array
     */
    protected function _convertGroupArray($selectOptions)
    {
        $data = array();
        if (is_array($selectOptions)) {
            $data[] = array('label' => __('Please Select'), 'value' => '');
            foreach ($selectOptions as $option) {
                $data[] = array('label' => $option['label'], 'value' => $option['code']);
            }
        }
        return $data;
    }

    /**
     * Render input field of the specific type : text, select, date, region, country
     *
     * @param array $data
     * @param string $field
     * @param string $value
     * @return string
     */
    public function renderField($data, $field, $value = null)
    {
        $element = '';
        if ($field && is_array($data)) {
            $type  = $data['type'];
            $name  = $data['name'];
            $id    = $data['id'];
            $value = $this->getEntity()->getFieldValue($id);
            $class = ($this->isAttributeRequired($data)) ? 'required-entry' : '';

            switch ($type) {
                case 'country' :
                    $element = $this->getCountryHtmlSelect($value, $name, $id, $class);
                    break;

                case 'region' :
                    $element = $this->getRegionHtmlSelectEmpty($name, $id, $value, $class);
                    $id = $this->_getElementId($id . '_text');
                    $name = $this->_getElementName($id);
                    $value = $this->getEntity()->getFieldValue($id);
                    $element .= $this->_getInputTextHtml($name, $id, $value, $class);
                    break;

                case 'date' :
                    $format = (isset($data['date_format'])) ? $data['date_format'] : '';
                    $element = $this->getCalendarDateHtml($name, $id, $value, $format, $class);
                    break;

                case 'select' :
                    $options = $this->_convertGroupArray($data['options']);
                    if (empty($value)) {
                        $value = (isset($data['default'])) ? $data['default'] : '';
                    }
                    $element = $this->getSelectHtml($options, $name, $id, $value, $class);
                    break;

                default :
                    $element = $this->_getInputTextHtml($name, $id, $value, $class);
                    break;
            }
        }
        return $element;
    }

    /**
     * Render "input text" field
     * @param string $name
     * @param string $id
     * @param string $value
     * @param string $class
     * @param string $params additional params
     *
     * @return string
     */
    protected function _getInputTextHtml($name, $id, $value = '', $class = '', $params = '')
    {
        $template = $this->getLayout()->getBlock('giftregistry_edit')->getInputTypeTemplate('text');
        $this->setInputName($name)
            ->setInputId($id)
            ->setInputValue($value)
            ->setInputClass($class)
            ->setInputParams($params);
        if ($template) {
            return $this->fetchView($template);
        }
    }

    /**
     * Return region select html element
     * @param string $name
     * @param string $id
     * @param string $value
     * @param string $class
     * @param string $params additional params
     */
    public function getRegionHtmlSelectEmpty($name, $id, $value = '', $class = '', $params = '', $default = '')
    {
        $template = $this->getLayout()->getBlock('giftregistry_edit')->getInputTypeTemplate('region');
        $this->setSelectRegionName($name)
            ->setSelectRegionId($id)
            ->setSelectRegionValue($value)
            ->setSelectRegionClass($class)
            ->setSelectRegionParams($params)
            ->setSelectRegionDefault($default);
        if ($template) {
            return $this->fetchView($template);
        }
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCreateActionUrl($this->getUrl('magento_giftregistry/index/addPost'));
        return parent::_toHtml();
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getAddGiftRegistryUrl()
    {
        return $this->getUrl('magento_giftregistry/index/addselect');
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getSaveActionUrl()
    {
        return $this->getUrl('magento_giftregistry/index/save');
    }

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedAttributes()
    {
        $attributes = $this->getCustomAttributes();
        if (!empty($attributes[$this->_prefix])) {
            return $this->_groupAttributes($attributes[$this->_prefix]);
        }
        return array();
    }
}
