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
     */
    public function emulateLayoutArea($areaCode)
    {
        $configuration = array(
            'Magento\Core\Model\Layout' => array(
                'arguments' => array(
                    'area' => array(
                        \Magento\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE => 'string',
                        'value' => $areaCode
                    )
                )
            )
        );
        $this->_objectManager->configure($configuration);
    }
}
