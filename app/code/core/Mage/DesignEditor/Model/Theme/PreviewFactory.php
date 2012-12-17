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
 * Visual Design Editor Preview Factory
 */
class Mage_DesignEditor_Model_Theme_PreviewFactory
{
    /**#@+
     * Preview modes
     */
    const TYPE_DEFAULT = 'default';
    const TYPE_DEMO    = 'demo';
    /**#@-*/

    /**
     * System Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create preview model
     *
     * @param string $type
     * @return Mage_DesignEditor_Model_Theme_Preview_Abstract
     * @throws Magento_Exception
     */
    public function create($type = self::TYPE_DEFAULT)
    {
        switch ($type) {
            case self::TYPE_DEFAULT:
                $preview = $this->_objectManager->create('Mage_DesignEditor_Model_Theme_Preview_Default');
                break;
            case self::TYPE_DEMO:
                $preview = $this->_objectManager->create('Mage_DesignEditor_Model_Theme_Preview_Demo');
                break;
            default:
                throw new Magento_Exception('Undefined Preview Type');
                break;
        }
        return $preview;
    }
}
