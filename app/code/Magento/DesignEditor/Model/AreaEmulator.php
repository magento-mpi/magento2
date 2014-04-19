<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model;

class AreaEmulator
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $areaCode
     * @return void
     */
    public function emulateLayoutArea($areaCode)
    {
        $configuration = array(
            'Magento\Framework\View\Layout' => array(
                'arguments' => array(
                    'area' => $areaCode
                )
            )
        );
        $this->_objectManager->configure($configuration);
    }
}
