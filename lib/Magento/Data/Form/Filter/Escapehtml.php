<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Form Input/Output Escape HTML entities Filter
 *
 * @category    Magento
 * @package     Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Filter;

class Escapehtml implements \Magento\Data\Form\Filter\FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value)
    {
        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        return htmlspecialchars($value);
    }
}
