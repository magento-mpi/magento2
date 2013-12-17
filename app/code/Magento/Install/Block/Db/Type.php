<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common database config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block\Db;

class Type extends \Magento\View\Element\Template
{
    /**
     * Db title
     *
     * @var string
     */
    protected $_title;

    /**
     * Install installer config
     *
     * @var \Magento\Install\Model\Installer\Config
     */
    protected $_installerConfig = null;

    /**
     * Install installer config
     *
     * @var \Magento\Session\Generic
     */
    protected $_session;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer\Config $installerConfig
     * @param \Magento\Session\Generic $session
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer\Config $installerConfig,
        \Magento\Session\Generic $session,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_installerConfig = $installerConfig;
        $this->_session = $session;
    }

    /**
     * Return Db title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Retrieve configuration form data object
     *
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = $this->_session->getConfigData(true);
            if (empty($data)) {
                $data = $this->_installerConfig->getFormData();
            } else {
                $data = new \Magento\Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }
}
