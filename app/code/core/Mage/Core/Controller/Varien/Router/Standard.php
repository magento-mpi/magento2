<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @TODO This is temporary router.
 * When required areas will be created this router should be removed or renamed and become a part of an area
 */
class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Base
{
    /**
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(Magento_ObjectManager $objectManager, array $options = array())
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param $configArea
     * @param $useRouterName
     */
    public function collectRoutes($configArea, $useRouterName)
    {
        $this->_area = $configArea;
        parent::collectRoutes($configArea, $useRouterName);
    }
}
