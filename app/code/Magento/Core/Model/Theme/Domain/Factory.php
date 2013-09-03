<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme domain model class
 */
class Magento_Core_Model_Theme_Domain_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_types = array(
        Magento_Core_Model_Theme::TYPE_PHYSICAL => 'Magento_Core_Model_Theme_Domain_Physical',
        Magento_Core_Model_Theme::TYPE_VIRTUAL  => 'Magento_Core_Model_Theme_Domain_Virtual',
        Magento_Core_Model_Theme::TYPE_STAGING  => 'Magento_Core_Model_Theme_Domain_Staging',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Theme_Domain_Virtual|Magento_Core_Model_Theme_Domain_Staging
     * @throws Magento_Core_Exception
     */
    public function create(Magento_Core_Model_Theme $theme)
    {
        if (!isset($this->_types[$theme->getType()])) {
            throw new Magento_Core_Exception(sprintf('Invalid type of theme domain model "%s"', $theme->getType()));
        }
        $class = $this->_types[$theme->getType()];
        return $this->_objectManager->create($class, array('theme' => $theme));
    }
}
