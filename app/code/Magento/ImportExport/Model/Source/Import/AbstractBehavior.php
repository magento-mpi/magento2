<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Source\Import;

/**
 * Source import behavior model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractBehavior implements \Magento\Option\ArrayInterface
{
    /**
     * Get array of possible values
     *
     * @abstract
     * @return array
     */
    abstract public function toArray();

    /**
     * Prepare and return array of option values
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array(array('label' => __('-- Please Select --'), 'value' => ''));
        $options = $this->toArray();
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $value => $label) {
                $optionArray[] = array('label' => $label, 'value' => $value);
            }
        }
        return $optionArray;
    }

    /**
     * Get current behaviour group code
     *
     * @abstract
     * @return string
     */
    abstract public function getCode();
}
