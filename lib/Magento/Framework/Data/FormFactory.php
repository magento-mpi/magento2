<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Form factory class
 */
class FormFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName;

    /**
     * Factory construct
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, $instanceName = 'Magento\Framework\Data\Form')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create form instance
     *
     * @param array $data
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception
     */
    public function create(array $data = array())
    {
        /** @var $form \Magento\Framework\Data\Form */
        $form = $this->_objectManager->create($this->_instanceName, $data);
        if (!$form instanceof \Magento\Framework\Data\Form) {
            throw new \Magento\Framework\Exception($this->_instanceName . ' doesn\'t extend \Magento\Framework\Data\Form');
        }
        return $form;
    }
}
