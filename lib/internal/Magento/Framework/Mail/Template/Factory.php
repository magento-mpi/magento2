<?php
/**
 * Mail Template Factory
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Mail\Template;

class Factory implements \Magento\Framework\Mail\Template\FactoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\Mail\TemplateInterface'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        return $this->_objectManager->create(
            $this->_instanceName,
            array('data' => array('template_id' => $identifier))
        );
    }
}
