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
 * @method \Magento\Directory\Model\Resource\Country _getResource()
 * @method \Magento\Directory\Model\Resource\Country getResource()
 * @method string getCountryId()
 * @method \Magento\Directory\Model\Country setCountryId(string $value)
 * @method string getIso2Code()
 * @method \Magento\Directory\Model\Country setIso2Code(string $value)
 * @method string getIso3Code()
 * @method \Magento\Directory\Model\Country setIso3Code(string $value)
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class Country extends \Magento\Core\Model\AbstractModel
{
    static public $_format = array();

    protected function _construct()
    {
        $this->_init('Magento\Directory\Model\Resource\Country');
    }

    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    public function getRegions()
    {
        return $this->getLoadedRegionCollection();
    }

    public function getLoadedRegionCollection()
    {
        $collection = $this->getRegionCollection();
        $collection->load();
        return $collection;
    }

    public function getRegionCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Directory\Model\Resource\Region\Collection');
        $collection->addCountryFilter($this->getId());
        return $collection;
    }

    public function formatAddress(\Magento\Object $address, $html=false)
    {
        //TODO: is it still used?
        $address->getRegion();
        $address->getCountry();



        $template = $this->getData('address_template_'.($html ? 'html' : 'plain'));
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

        $filter = new \Magento\Filter\Template\Simple();
        $addressText = $filter->setData($address->getData())->filter($template);

        if ($html) {
            $addressText = preg_replace('#(<br\s*/?>\s*){2,}#im', '<br/>', $addressText);
        } else {
            $addressText = preg_replace('#(\n\s*){2,}#m', "\n", $addressText);
        }

        return $addressText;
    }

    /**
     * Retrive formats for
     *
     * @return \Magento\Directory\Model\Resource\Country\Format\Collection
     */
    public function getFormats()
    {
        if (!isset(self::$_format[$this->getId()]) && $this->getId()) {
            self::$_format[$this->getId()] = \Mage::getModel('Magento\Directory\Model\Country\Format')
                                                ->getCollection()
                                                ->setCountryFilter($this)
                                                ->load();
        }

        if (isset(self::$_format[$this->getId()])) {
            return self::$_format[$this->getId()];
        }

        return null;
    }

    /**
     * Retrive format
     *
     * @param string $type
     * @return \Magento\Directory\Model\Country\Format
     */
    public function getFormat($type)
    {
        if ($this->getFormats()) {
            foreach ($this->getFormats() as $format) {
                if ($format->getType()==$type) {
                    return $format;
                }
            }
        }
        return null;
    }

    public function getName()
    {
        if (!$this->getData('name')) {
            $this->setData(
                'name',
                \Mage::app()->getLocale()->getCountryTranslation($this->getId())
            );
        }
        return $this->getData('name');
    }

}
