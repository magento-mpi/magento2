<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
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
