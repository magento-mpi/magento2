<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country model
 *
 * @method Magento_Directory_Model_Resource_Country _getResource()
 * @method Magento_Directory_Model_Resource_Country getResource()
 * @method string getCountryId()
 * @method Magento_Directory_Model_Country setCountryId(string $value)
 * @method string getIso2Code()
 * @method Magento_Directory_Model_Country setIso2Code(string $value)
 * @method string getIso3Code()
 * @method Magento_Directory_Model_Country setIso3Code(string $value)
 */
class Magento_Directory_Model_Country extends Magento_Core_Model_Abstract
{
    /**
     * @var array
     */
    static public $_format = array();

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Directory_Model_Country_FormatFactory
     */
    protected $_formatFactory;

    /**
     * @var Magento_Directory_Model_Resource_Region_CollectionFactory
     */
    protected $_regionCollFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Directory_Model_Country_FormatFactory $formatFactory
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Directory_Model_Country_FormatFactory $formatFactory,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->_locale = $locale;
        $this->_formatFactory = $formatFactory;
        $this->_regionCollFactory = $regionCollFactory;
    }

    protected function _construct()
    {
        $this->_init('Magento_Directory_Model_Resource_Country');
    }

    /**
     * @param string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function getRegions()
    {
        return $this->getLoadedRegionCollection();
    }

    /**
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function getLoadedRegionCollection()
    {
        $collection = $this->getRegionCollection();
        $collection->load();
        return $collection;
    }

    /**
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function getRegionCollection()
    {
        $collection = $this->_regionCollFactory->create();
        $collection->addCountryFilter($this->getId());
        return $collection;
    }

    /**
     * @param Magento_Object $address
     * @param bool $html
     * @return string
     */
    public function formatAddress(Magento_Object $address, $html = false)
    {
        //TODO: is it still used?
        $address->getRegion();
        $address->getCountry();



        $template = $this->getData('address_template_' . ($html ? 'html' : 'plain'));
        if (empty($template)) {
            if (!$this->getId()) {
                $template = '{{firstname}} {{lastname}}';
            } elseif (!$html) {
                $template = "{{firstname}} {{lastname}}
{{company}}
{{street1}}
{{street2}}
{{city}}, {{region}} {{postcode}}";
            } else {
                $template = "{{firstname}} {{lastname}}<br/>
{{street}}<br/>
{{city}}, {{region}} {{postcode}}<br/>
T: {{telephone}}";
            }
        }

        $filter = new Magento_Filter_Template_Simple();
        $addressText = $filter->setData($address->getData())->filter($template);

        if ($html) {
            $addressText = preg_replace('#(<br\s*/?>\s*){2,}#im', '<br/>', $addressText);
        } else {
            $addressText = preg_replace('#(\n\s*){2,}#m', "\n", $addressText);
        }

        return $addressText;
    }

    /**
     * Retrieve formats for
     *
     * @return Magento_Directory_Model_Resource_Country_Format_Collection
     */
    public function getFormats()
    {
        if (!isset(self::$_format[$this->getId()]) && $this->getId()) {
            self::$_format[$this->getId()] = $this->_formatFactory->create()
                ->getCollection()->setCountryFilter($this)->load();
        }

        if (isset(self::$_format[$this->getId()])) {
            return self::$_format[$this->getId()];
        }

        return null;
    }

    /**
     * Retrieve format
     *
     * @param string $type
     * @return Magento_Directory_Model_Country_Format
     */
    public function getFormat($type)
    {
        if ($this->getFormats()) {
            foreach ($this->getFormats() as $format) {
                if ($format->getType() == $type) {
                    return $format;
                }
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->getData('name')) {
            $this->setData('name', $this->_locale->getCountryTranslation($this->getId()));
        }
        return $this->getData('name');
    }

}
