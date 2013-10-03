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
 * Install state block
 *
 * @category   Magento
 * @package    Magento_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

class State extends \Magento\Core\Block\Template
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
     * Core Cookie
     *
     * @var \Magento\Core\Model\Cookie
     */
    protected $_cookie;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Install\Model\Wizard $wizard
     * @param \Magento\Core\Model\Cookie $cookie
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Install\Model\Wizard $wizard,
        \Magento\Core\Model\Cookie $cookie,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_wizard = $wizard;
        $this->_cookie = $cookie;

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
        $session = $this->_cookie->get('magento_downloader_session');
        return $session ? true : false;
    }
}
