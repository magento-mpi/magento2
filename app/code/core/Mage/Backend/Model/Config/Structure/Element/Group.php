<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Group
    extends Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
{
    /**
     * Should group fields be cloned
     *
     * @return bool
     */
    public function shouldCloneFields()
    {
        return (isset($groupConfig['clone_fields']) && !empty($groupConfig['clone_fields']));
    }


    /**
     * Retrieve clone model
     *
     * @return mixed
     */
    public function getCloneModel()
    {
        if (!isset($groupConfig['clone_model']) || !$groupConfig['clone_model']) {
            Mage::throwException('Config form fieldset clone model required to be able to clone fields');
        }
        $cloneModel = $this->_objectFactory->getModelInstance($groupConfig['clone_model']);
        return $cloneModel;
    }

}
