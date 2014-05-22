<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

/**
 * Builder for the TaxRate Service Data Object
 *
 * @method TaxRate create()
 */
class TaxRateBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * ZipRange builder
     *
     * @var \Magento\Tax\Service\V1\Data\ZipRangeBuilder
     */
    protected $zipRangeBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Tax\Service\V1\Data\ZipRangeBuilder $zipRangeBuilder
     */
    public function __construct(
        \Magento\Tax\Service\V1\Data\ZipRangeBuilder $zipRangeBuilder
    ) {
        parent::__construct();
        $this->zipRangeBuilder = $zipRangeBuilder;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->_set(TaxRate::KEY_ID, $id);
        return $this;
    }

    /**
     * Set country id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        $this->_set(TaxRate::KEY_COUNTRY_ID, $countryId);
        return $this;
    }

    /**
     * Set region id
     *
     * @param int $regionId
     * @return $this
     */
    public function setRegionId($regionId)
    {
        $this->_set(TaxRate::KEY_REGION_ID, $regionId);
        return $this;
    }

    /**
     * Set zip
     *
     * @param $zip
     * @return $this
     */
    public function setZip($zip)
    {
        $this->_set(TaxRate::KEY_ZIP, $zip);
        return $this;
    }

    /**
     * Set zip range
     *
     * @param \Magento\Tax\Service\V1\Data\ZipRange $zipRange
     * @return $this
     */
    public function setZipRange($zipRange)
    {
        $this->_set(TaxRate::KEY_ZIP_RANGE, $zipRange);
        return $this;
    }

    /**
     * Set tax rate in percentage
     *
     * @param float $rate
     * @return $this
     */
    public function setPercentageRate($rate)
    {
        $this->_set(TaxRate::KEY_PERCENTAGE_RATE, $rate);
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(TaxRate::KEY_ZIP_RANGE, $data)) {
            $data[TaxRate::KEY_ZIP_RANGE] =
                $this->zipRangeBuilder->populateWithArray($data[TaxRate::KEY_ZIP_RANGE])->create();
        }
        return parent::_setDataValues($data);
    }
}
