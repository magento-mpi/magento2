<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

class Config extends \Magento\Install\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'config.phtml';

    /**
     * Install installer config
     *
     * @var \Magento\Install\Model\Installer\Config
     */
    protected $_installerConfig = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Install\Model\Installer\Config $installerConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Framework\Session\Generic $session,
        \Magento\Install\Model\Installer\Config $installerConfig,
        array $data = array()
    ) {
        parent::__construct($context, $installer, $installWizard, $session, $data);
        $this->_installerConfig = $installerConfig;
    }

    /**
     * Retrieve form data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/configPost');
    }

    /**
     * Retrieve configuration form data object
     *
     * @return \Magento\Framework\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = $this->_session->getConfigData(true);
            if (empty($data)) {
                $data = $this->_installerConfig->getFormData();
            } else {
                $data = new \Magento\Framework\Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function getSkipUrlValidation()
    {
        return $this->_session->getSkipUrlValidation();
    }

    /**
     * @return bool
     */
    public function getSkipBaseUrlValidation()
    {
        return $this->_session->getSkipBaseUrlValidation();
    }

    /**
     * @return array
     */
    public function getSessionSaveOptions()
    {
        return array('files' => __('File System'), 'db' => __('Database'));
    }

    /**
     * @return string
     */
    public function getSessionSaveSelect()
    {
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'config[session_save]'
        )->setId(
            'session_save'
        )->setTitle(
            __('Save Session Files In')
        )->setClass(
            'required-entry'
        )->setOptions(
            $this->getSessionSaveOptions()
        )->getHtml();
        return $html;
    }
}
