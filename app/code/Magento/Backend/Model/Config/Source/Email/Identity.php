<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Email_Identity implements Magento_Core_Model_Option_ArrayInterface
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
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * @param Magento_Backend_Model_Config_Structure $configStructure
     */
    public function __construct(Magento_Backend_Model_Config_Structure $configStructure)
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
            /** @var $section Magento_Backend_Model_Config_Structure_Element_Section */
            $section = $this->_configStructure->getElement('trans_email');

            /** @var $group Magento_Backend_Model_Config_Structure_Element_Group */
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
