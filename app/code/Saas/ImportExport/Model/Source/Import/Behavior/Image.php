<?php
/**
 * Import behavior source model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Source_Import_Behavior_Image
    extends Mage_ImportExport_Model_Source_Import_BehaviorAbstract
{
    /**
     * Behaviour code
     */
    const BEHAVIOUR_CODE_IMAGES = 'images';

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @param Saas_ImportExport_Helper_Data $helper
     * @param array $data
     */
    public function __construct(Saas_ImportExport_Helper_Data $helper, array $data = array())
    {
        parent::__construct($data);

        $this->_helper = $helper;
    }

    /**
     * Get possible behaviours
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Mage_ImportExport_Model_Import::BEHAVIOR_APPEND => __('Add/Update Images'),
        );
    }

    /**
     * Get behaviour code
     *
     * @return string
     */
    public function getCode()
    {
        return self::BEHAVIOUR_CODE_IMAGES;
    }
}
