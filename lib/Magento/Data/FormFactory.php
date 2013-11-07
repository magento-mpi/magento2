<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Data;

/**
 * Form factory class
 */
class FormFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
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
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Data\Form'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create form instance
     *
     * @param array $data
     * @return \Magento\Data\Form
     * @throws \Magento\Exception
     */
    public function create(array $data = array())
    {
        /** @var $form \Magento\Data\Form */
        $form = $this->_objectManager->create($this->_instanceName, $data);
        if (!$form instanceof \Magento\Data\Form) {
            throw new \Magento\Exception($this->_instanceName . ' doesn\'t extend \Magento\Data\Form');
        }
        return $form;
    }
}
