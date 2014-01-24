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
     * @deprecated All new code should use renderArray based on Metadata service
     * @param string[] $addressAttributes
     * @param \Magento\Directory\Model\Country\Format $format
     * @return string
     */
    public function render($addressAttributes, $format = null);

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
