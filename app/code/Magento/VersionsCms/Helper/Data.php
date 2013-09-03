<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base helper
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */

class Magento_VersionsCms_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Array of admin users in system
     * @var array
     */
    protected $_usersHash = null;

    /**
     * Retrieve array of admin users in system
     *
     * @return array
     */
    public function getUsersArray($addEmptyUser = false)
    {
        if (!$this->_usersHash) {
            $collection = Mage::getModel('Magento_User_Model_User')->getCollection();
            $this->_usersHash = array();

            if ($addEmptyUser) {
                $this->_usersHash[''] = '';
            }

            foreach ($collection as $user) {
                $this->_usersHash[$user->getId()] = $user->getUsername();
            }
        }

        return $this->_usersHash;
    }

    /**
     * Get version's access levels with labels.
     *
     * @return array
     */
    public function getVersionAccessLevels()
    {
        return array(
            Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PRIVATE => __('Private'),
            Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PROTECTED => __('Protected'),
            Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC => __('Public')
        );
    }

    /**
     * Recursively walk through container (form or fieldset)
     * and add to each element new onChange method.
     * Element will be skipped if its type passed in $excludeTypes parameter.
     *
     * @param \Magento\Data\Form\AbstractForm $container
     * @param string $onChange
     * @param string|array $excludeTypes
     */
    public function addOnChangeToFormElements($container, $onChange, $excludeTypes = array('hidden'))
    {
        if (!is_array($excludeTypes)) {
            $excludeTypes = array($excludeTypes);
        }

        foreach ($container->getElements()as $element) {
            if ($element->getType() == 'fieldset') {
                $this->addOnChangeToFormElements($element, $onChange, $excludeTypes);
            } else {
                if (!in_array($element->getType(), $excludeTypes)) {
                    if ($element->hasOnchange()) {
                        $onChangeBefore = $element->getOnchange() . ';';
                    } else {
                        $onChangeBefore = '';
                    }
                    $element->setOnchange($onChangeBefore . $onChange);
                }
            }
        }
    }

    /**
     * Get 'Delete Multiple Hierarchies' text
     *
     * @return string
     */
    public function getDeleteMultipleHierarchiesText()
    {
        return __('Delete Multiple Hierarchies');
    }
}
