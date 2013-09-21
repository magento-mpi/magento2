<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page layouts source
 *
 * @category   Magento
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Model_Source_Layout implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Page layout options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Default option
     * @var string
     */
    protected $_defaultValue = null;

    /**
     * Retrieve page layout options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getSingleton('Magento_Page_Model_Config')->getPageLayouts() as $layout) {
                $this->_options[$layout->getCode()] = $layout->getLabel();
                if ($layout->getIsDefault()) {
                    $this->_defaultValue = $layout->getCode();
                }
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve page layout options array
     *
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        $options = array();

        foreach ($this->getOptions() as $value => $label) {
            $options[] = array(
                'label' => $label,
                'value' => $value
            );
        }

        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>__('-- Please Select --')));
        }

        return $options;
    }

    /**
     * Default options value getter
     * @return string
     */
    public function getDefaultValue()
    {
        $this->getOptions();
        return $this->_defaultValue;
    }
}
