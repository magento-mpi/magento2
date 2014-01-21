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
 * Address renderer interface
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Address\Renderer;

interface RendererInterface
{
    /**
     * Set format type object
     *
     * @param \Magento\Object $type
     */
    function setType(\Magento\Object $type);

    /**
     * Retrieve format type object
     *
     * @return \Magento\Object
     */
    function getType();

    /**
     * Render address
     *
     * @param \Magento\Customer\Model\Address\AbstractAddress $address
     * @return mixed
     */
    function render(\Magento\Customer\Model\Address\AbstractAddress $address);

    /**
     * Get a format object for a given address attributes, based on the type set earlier.
     *
     * @param null|array $addressAttributes
     * @return \Magento\Directory\Model\Country\Format
     */
    public function getFormatArray($addressAttributes = null);

    /**
     * Render address by attribute array
     *
     * @param array $addressAttributes
     * @param \Magento\Directory\Model\Country\Format $format
     * @return string
     */
    public function renderArray($addressAttributes, $format = null);
}
