<?php
/**
 * Mail Template Factory
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail\Template;

class Factory implements \Magento\Mail\Template\FactoryInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\ObjectManager $objectManager, $instanceName = 'Magento\Mail\TemplateInterface')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        $template = $this->_objectManager->create($this->_instanceName);
        if (is_numeric($identifier)) {
            $template->load($identifier);
        } else {
            $template->loadDefault($identifier);
        }
        return $template;
    }
}
