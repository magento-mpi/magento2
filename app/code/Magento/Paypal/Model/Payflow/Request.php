<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Link request model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Paypal_Model_Payflow_Request extends Magento_Object
{
    /**
     * Set/Get attribute wrapper
     * Also add length path if key contains = or &
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        $key = $this->_underscore(substr($method,3));
        if (isset($args[0]) && (strstr($args[0], '=') || strstr($args[0], '&'))) {
            $key .= '[' . strlen($args[0]) . ']';
        }
        switch (substr($method, 0, 3)) {
            case 'get' :
                //Magento_Profiler::start('GETTER: '.get_class($this).'::'.$method);
                $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                //Magento_Profiler::stop('GETTER: '.get_class($this).'::'.$method);
                return $data;

            case 'set' :
                //Magento_Profiler::start('SETTER: '.get_class($this).'::'.$method);
                $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                //Magento_Profiler::stop('SETTER: '.get_class($this).'::'.$method);
                return $result;

            case 'uns' :
                //Magento_Profiler::start('UNS: '.get_class($this).'::'.$method);
                $result = $this->unsetData($key);
                //Magento_Profiler::stop('UNS: '.get_class($this).'::'.$method);
                return $result;

            case 'has' :
                //Magento_Profiler::start('HAS: '.get_class($this).'::'.$method);
                //Magento_Profiler::stop('HAS: '.get_class($this).'::'.$method);
                return isset($this->_data[$key]);
        }
        throw new Magento_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }
}
