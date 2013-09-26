<?php
/**
 * Bundle Option Type Source Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Source\Option;

class Type implements \Magento\Core\Model\Option\ArrayInterface
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
