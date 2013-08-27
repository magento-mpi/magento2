<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor request data
 */
class Magento_DesignEditor_Model_Config_Backend_File_RequestData
    implements Magento_Backend_Model_Config_Backend_File_RequestData_Interface
{
    /**
     * Retrieve uploaded file tmp name by path
     *
     * @param string $path
     * @return string
     */
    public function getTmpName($path)
    {
        return $this->_getParam('tmp_name', $path);
    }

    /**
     * Retrieve uploaded file name by path
     *
     * @param string $path
     * @return string
     */
    public function getName($path)
    {
        return $this->_getParam('name', $path);
    }

    /**
     * Get $_FILES superglobal value by path
     *
     * @param string $paramName
     * @return string
     */
    protected function _getParam($paramName)
    {
        $logoImage = reset($_FILES);
        if (empty($logoImage)) {
            return null;
        }
        return $logoImage[$paramName];
    }
}
