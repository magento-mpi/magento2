<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

abstract class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * Installer model
     *
     * @var \Magento\Install\Model\Installer
     */
    protected $_installer;

    /**
     * Wizard model
     *
     * @var \Magento\Install\Model\Wizard
     */
    protected $_installWizard;

    /**
     * Install session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Framework\Session\Generic $session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Framework\Session\Generic $session,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_installer = $installer;
        $this->_installWizard = $installWizard;
        $this->_session = $session;
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve installer model
     *
     * @return \Magento\Install\Model\Installer
     */
    public function getInstaller()
    {
        return $this->_installer;
    }

    /**
     * Retrieve wizard model
     *
     * @return \Magento\Install\Model\Wizard
     */
    public function getWizard()
    {
        return $this->_installWizard;
    }

    /**
     * Retrieve current installation step
     *
     * @return \Magento\Framework\Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
