<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source\Email;

class Identity implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Email Identity options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Configuration structure
     *
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     */
    public function __construct(\Magento\Backend\Model\Config\Structure $configStructure)
    {
        $this->_configStructure = $configStructure;
    }

    /**
     * Retrieve list of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            /** @var $section \Magento\Backend\Model\Config\Structure\Element\Section */
            $section = $this->_configStructure->getElement('trans_email');

            /** @var $group \Magento\Backend\Model\Config\Structure\Element\Group */
            foreach ($section->getChildren() as $group) {
                $this->_options[] = array(
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $group->getId()),
                    'label' => $group->getLabel()
                );
            }
            ksort($this->_options);
        }
        return $this->_options;
    }
}
