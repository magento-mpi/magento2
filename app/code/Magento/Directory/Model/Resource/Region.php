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
 * Directory Region Resource Model
 */
namespace Magento\Directory\Model\Resource;

class Region extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Table with localized region names
     *
     * @var string
     */
    protected $_regionNameTable;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Locale\ResolverInterface $localeResolver
    ) {
        parent::__construct($resource);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Define main and locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_country_region', 'region_id');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Model\AbstractModel $object
     * @return \Magento\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select  = parent::_getLoadSelect($field, $value, $object);
        $adapter = $this->_getReadAdapter();

        $locale       = $this->_localeResolver->getLocaleCode();
        $systemLocale = \Magento\Core\Model\App::DISTRO_LOCALE_CODE;

        $regionField = $adapter->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $adapter->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            array('lrn' => $this->_regionNameTable),
            "{$regionField} = lrn.region_id AND {$condition}",
            array());

        if ($locale != $systemLocale) {
            $nameExpr  = $adapter->getCheckSql('lrn.region_id is null', 'srn.name', 'lrn.name');
            $condition = $adapter->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                array('srn' => $this->_regionNameTable),
                "{$regionField} = srn.region_id AND {$condition}",
                array('name' => $nameExpr));
        } else {
            $select->columns(array('name'), 'lrn');
        }

        return $select;
    }

    /**
     * Load object by country id and code or default name
     *
     * @param \Magento\Model\AbstractModel $object
     * @param int $countryId
     * @param string $value
     * @param string $field
     * @return $this
     */
    protected function _loadByCountry($object, $countryId, $value, $field)
    {
        $adapter        = $this->_getReadAdapter();
        $locale         = $this->_localeResolver->getLocaleCode();
        $joinCondition  = $adapter->quoteInto('rname.region_id = region.region_id AND rname.locale = ?', $locale);
        $select         = $adapter->select()
            ->from(array('region' => $this->getMainTable()))
            ->joinLeft(
                array('rname' => $this->_regionNameTable),
                $joinCondition,
                array('name'))
            ->where('region.country_id = ?', $countryId)
            ->where("region.{$field} = ?", $value);

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Loads region by region code and country id
     *
     * @param \Magento\Directory\Model\Region $region
     * @param string $regionCode
     * @param string $countryId
     *
     * @return $this
     */
    public function loadByCode(\Magento\Directory\Model\Region $region, $regionCode, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string)$regionCode, 'code');
    }

    /**
     * Load data by country id and default region name
     *
     * @param \Magento\Directory\Model\Region $region
     * @param string $regionName
     * @param string $countryId
     * @return $this
     */
    public function loadByName(\Magento\Directory\Model\Region $region, $regionName, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string)$regionName, 'default_name');
    }
}
