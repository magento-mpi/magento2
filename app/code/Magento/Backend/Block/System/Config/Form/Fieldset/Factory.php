<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * \Magento\Backend\Block\System\Config\Form\Fieldset object factory
 */
namespace Magento\Backend\Block\System\Config\Form\Fieldset;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param array $data
     * @return \Magento\Backend\Block\System\Config\Form\Fieldset
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Backend\Block\System\Config\Form\Fieldset', $data);
    }
}
