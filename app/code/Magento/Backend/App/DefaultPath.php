<?php
/**
 * Default application path for backend area
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

class DefaultPath implements \Magento\App\DefaultPathInterface
{
    /**
     * @var array
     */
    protected $_parts;

    /**
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(\Magento\Backend\App\ConfigInterface $config)
    {
        $pathParts = explode('/', $config->getValue('web/default/admin'));

        $this->_parts = array(
            'area' => isset($pathParts[0]) ? $pathParts[0] : '',
            'module' => isset($pathParts[1]) ? $pathParts[1] : 'admin',
            'controller' => isset($pathParts[2]) ? $pathParts[2] : 'index',
            'action' => isset($pathParts[3]) ? $pathParts[3] : 'index'
        );
    }

    /**
     * Retrieve default path part by code
     *
     * @param string $code
     * @return string
     */
    public function getPart($code)
    {
        return isset($this->_parts[$code]) ? $this->_parts[$code] : null;
    }
}
