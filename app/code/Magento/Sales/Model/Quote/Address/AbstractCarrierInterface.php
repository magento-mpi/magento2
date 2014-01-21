<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address;

interface AbstractCarrierInterface
{
    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field);

    /**
     * Retrieve config flag for store by field
     *
     * @param string $field
     * @return bool
     */
    public function getConfigFlag($field);

    /**
     * Collect and get rates
     *
     * @abstract
     * @param \Magento\Sales\Model\Quote\Address\RateRequest $request
     * @return \Magento\Object|bool|null
     */
    public function collectRates(\Magento\Sales\Model\Quote\Address\RateRequest $request);

    /**
     * Do request to shipment
     * Implementation must be in overridden method
     *
     * @param \Magento\Object $request
     * @return \Magento\Object
     */
    public function requestToShipment($request);

    /**
     * Do return of shipment
     * Implementation must be in overridden method
     *
     * @param $request
     * @return \Magento\Object
     */
    public function returnOfShipment($request);

    /**
     * Return container types of carrier
     *
     * @param \Magento\Object|null $params
     * @return array
     */
    public function getContainerTypes(\Magento\Object $params = null);

    /**
     * Get Container Types, that could be customized
     *
     * @return array
     */
    public function getCustomizableContainerTypes();

    /**
     * Return delivery confirmation types of carrier
     *
     * @param \Magento\Object|null $params
     * @return array
     */
    public function getDeliveryConfirmationTypes(\Magento\Object $params = null);

    /**
     * @param \Magento\Sales\Model\Quote\Address\RateRequest $request
     * @return $this|bool|false|\Magento\Core\Model\AbstractModel
     */
    public function checkAvailableShipCountries(\Magento\Sales\Model\Quote\Address\RateRequest $request);

    /**
     * Processing additional validation to check is carrier applicable.
     *
     * @param \Magento\Sales\Model\Quote\Address\RateRequest $request
     * @return $this|\Magento\Sales\Model\Quote\Address\RateResult\Error|boolean
     */
    public function proccessAdditionalValidation(\Magento\Sales\Model\Quote\Address\RateRequest $request);

    /**
     * Determine whether current carrier enabled for activity
     *
     * @return bool
     */
    public function isActive();

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @return bool
     */
    public function isFixed();

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return bool
     */
    public function isTrackingAvailable();

    /**
     * Check if carrier has shipping label option available
     *
     * @return bool
     */
    public function isShippingLabelsAvailable();

    /**
     *  Retrieve sort order of current carrier
     *
     * @return mixed
     */
    public function getSortOrder();

    /**
     * Calculate price considering free shipping and handling fee
     *
     * @param string $cost
     * @param string $method
     * @return float|string
     */
    public function getMethodPrice($cost, $method = '');

    /**
     * Get the handling fee for the shipping + cost
     *
     * @param float $cost
     * @return float final price for shipping method
     */
    public function getFinalPriceWithHandlingFee($cost);

    /**
     *  Return weight in pounds
     *
     *  @param integer Weight in someone measure
     *  @return float Weight in pounds
     */
    public function convertWeightToLbs($weight);

    /**
     * Set the number of boxes for shipping
     *
     * @param int|float $weight
     * @return int|float weight
     */
    public function getTotalNumOfBoxes($weight);

    /**
     * Is state province required
     *
     * @return bool
     */
    public function isStateProvinceRequired();

    /**
     * Check if city option required
     *
     * @return bool
     */
    public function isCityRequired();

    /**
     * Determine whether zip-code is required for the country of destination
     *
     * @param string|null $countryId
     * @return bool
     */
    public function isZipCodeRequired($countryId = null);

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag();

    /**
     * Used to call debug method from not Payment Method context
     *
     * @param mixed $debugData
     */
    public function debugData($debugData);

    /**
     * Getter for carrier code
     *
     * @return string
     */
    public function getCarrierCode();

    /**
     * Return content types of package
     *
     * @param \Magento\Object $params
     * @return array
     */
    public function getContentTypes(\Magento\Object $params);
}
