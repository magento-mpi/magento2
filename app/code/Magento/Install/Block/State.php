<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Block;

/**
 * Install state block
 */
class State extends \Magento\View\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'state.phtml';

    /**
     * Install Wizard
     *
     * @var \Magento\Install\Model\Wizard
     */
    protected $_wizard;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Install\Model\Wizard $wizard
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Install\Model\Wizard $wizard,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_wizard = $wizard;

        $this->assign('steps', $this->_wizard->getSteps());
    }

    /**
     * Get previous downloader steps
     *
     * @return array
     */
    public function getDownloaderSteps()
    {
        if ($this->isDownloaderInstall()) {
            $steps = array(
                __('Welcome'),
                __('Validation'),
                __('Magento Connect Manager Deployment'),
            );
            return $steps;
        } else {
            return array();
        }
    }

    /**
     * Checks for Magento Connect Manager installation method
     *
     * @return bool
     */
    public function isDownloaderInstall()
    {
        $session = $this->_request->getCookie('magento_downloader_session', false);
        return $session ? true : false;
    }
}
