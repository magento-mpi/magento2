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
     * @param string[] $addressAttributes
     * @param \Magento\Directory\Model\Country\Format $format
     * @return string
     */
    public function render($addressAttributes, $format = null);
}
