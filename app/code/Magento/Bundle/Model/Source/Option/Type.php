<?php
/**
 * Bundle Option Type Source Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Bundle_Model_Source_Option_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Get Bundle Option Type
     *
     * @return array
     */
    public function toOptionArray()
    {
        $types = array();
        foreach ($this->_options as $value => $label) {
            $types[] = array(
                'label' => $label,
                'value' => $value
            );
        }
        return $types;
    }
}
