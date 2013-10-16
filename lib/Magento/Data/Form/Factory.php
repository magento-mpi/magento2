<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Data\Form;

/**
 * Form factory class
 */
class Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Session instance
     *
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_session;

    /**
     * Factory construct
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Session\AbstractSession $session,
        $instanceName = 'Magento\Data\Form'
    ) {
        $this->_objectManager = $objectManager;
        $this->_session = $session;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create form instance
     *
     * @param array $data
     * @return \Magento\Data\Form
     */
    public function create(array $data = array())
    {
        /** @var $form \Magento\Data\Form */
        $form = $this->_objectManager->create($this->_instanceName, $data);
        $form->setSession($this->_session);
        return $form;
    }
}
