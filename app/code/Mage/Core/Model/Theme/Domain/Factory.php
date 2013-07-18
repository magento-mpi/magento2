<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme domain model class
 */
class Mage_Core_Model_Theme_Domain_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_types = array(
        Mage_Core_Model_Theme::TYPE_PHYSICAL => 'Mage_Core_Model_Theme_Domain_Physical',
        Mage_Core_Model_Theme::TYPE_VIRTUAL  => 'Mage_Core_Model_Theme_Domain_Virtual',
        Mage_Core_Model_Theme::TYPE_STAGING  => 'Mage_Core_Model_Theme_Domain_Staging',
    );

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme_Domain_Virtual|Mage_Core_Model_Theme_Domain_Staging
     * @throws Mage_Core_Exception
     */
    public function create(Mage_Core_Model_Theme $theme)
    {
        if (!isset($this->_types[$theme->getType()])) {
            throw new Mage_Core_Exception(sprintf('Invalid type of theme domain model "%s"', $theme->getType()));
        }
        $class = $this->_types[$theme->getType()];
        return $this->_objectManager->create($class, array('theme' => $theme));
    }
}
