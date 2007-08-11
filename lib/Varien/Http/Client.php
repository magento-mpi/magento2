<?php
/**
 * Varien HTTP Client
 *
 * @package     Varien
 * @subpackage  Http
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Varien_Http_Client extends Zend_Http_Client
{
    public function __construct($uri = null, $config = null)
    {
        $this->config['useragent'] = 'Varien_Http_Client';
        
        parent::__construct($uri, $config);
    }
    
    protected function _trySetCurlAdapter()
    {
        if (extension_loaded('curl')) {
            $this->setAdapter(new Varien_Http_Adapter_Curl());
        }
        return $this;
    }
    
    public function request($method = null)
    {
        $this->_trySetCurlAdapter();
        return parent::request($method);
    }
}
