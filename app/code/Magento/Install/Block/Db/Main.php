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
 * Database config installation block
 */
namespace Magento\Install\Block\Db;

class Main extends \Magento\View\Element\Template
{
    /**
     * Array of Database blocks keyed by name
     *
     * @var array
     */
    protected $_databases = array();

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
     * Adding customized database block template for database model type
     *
     * @param  string $type database type
     * @param  string $block database block type
     * @param  string $template
     * @return \Magento\Install\Block\Db\Main
     */
    public function addDatabaseBlock($type, $block, $template)
    {
        $this->_databases[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'instance'  => null
        );

        return $this;
    }

    /**
     * Retrieve database block by type
     *
     * @param  string $type database model type
     * @return bool|\Magento\View\Element\Template
     */
    public function getDatabaseBlock($type)
    {
        $block = false;
        if (isset($this->_databases[$type])) {
            if ($this->_databases[$type]['instance']) {
                $block = $this->_databases[$type]['instance'];
            } else {
                $block = $this->getLayout()->createBlock($this->_databases[$type]['block'])
                    ->setTemplate($this->_databases[$type]['template'])
                    ->setIdPrefix($type);
                $this->_databases[$type]['instance'] = $block;
            }
        }
        return $block;
    }

    /**
     * Retrieve database blocks
     *
     * @return array
     */
    public function getDatabaseBlocks()
    {
        $databases = array();
        foreach (array_keys($this->_databases) as $type) {
            $databases[] = $this->getDatabaseBlock($type);
        }
        return $databases;
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
