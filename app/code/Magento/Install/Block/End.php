<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation ending block
 */
namespace Magento\Install\Block;

class End extends \Magento\Install\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'end.phtml';

    /**
     * @var \Magento\Install\Model\Survey
     */
    protected $_survey;

    /**
     * Cryptographic key
     *
     * @var string
     */
    protected $_cryptKey;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Install\Model\Survey $survey
     * @param string $cryptKey
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Framework\Session\Generic $session,
        \Magento\Install\Model\Survey $survey,
        $cryptKey,
        array $data = array()
    ) {
        $this->_cryptKey = $cryptKey;
        parent::__construct($context, $installer, $installWizard, $session, $data);
        $this->_survey = $survey;
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = $this->_cryptKey;
            $this->setData('encryption_key', $key);
        }
        return $key;
    }

    /**
     * Return url for iframe source
     *
     * @return string|null
     */
    public function getIframeSourceUrl()
    {
        if (!$this->_survey->isSurveyUrlValid() || $this->_installer->getHideIframe()) {
            return null;
        }
        return $this->_survey->getSurveyUrl();
    }
}
