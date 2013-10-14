<?php

namespace Magento\View\Layout;

class Argument
{
    /**
     * Update args according to its type
     *
     * @param array $element
     * @return array
     */
    public function extractArgs(array $element)
    {
        $arguments = isset($element['arguments']) ? $element['arguments'] : array();
        $result = array();

        foreach ($arguments as $argument) {
            $key = (string)$argument['name'];
            if (isset($argument['translate'])) {
                $result[$key] = $this->_translateArgument($argument);
            } elseif (isset($argument['helper'])) {
                $result[$key] = $this->_getArgsByHelper($argument);
            } else {
                $result[$key] = $argument['value'];
            }
        }

        return $result;
    }

    /**
     * Gets arguments using helper method
     *
     * @param array $argument
     * @return mixed
     */
    protected function _getArgsByHelper(array $argument)
    {
        return $argument['value'];
        //$helper = $argument['helper'];
        //list($helperName, $helperMethod) = explode('::', $helper);
        //return call_user_func_array(array(Mage::helper($helperName), $helperMethod), $argument['value']);
    }

    /**
     * Translate argument if needed
     *
     * @param array $argument
     * @return string
     */
    protected function _translateArgument(array $argument)
    {
        return __($argument['value']);
    }
}
