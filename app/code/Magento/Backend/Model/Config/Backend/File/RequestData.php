<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Backend_File_RequestData
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
     * @param string $path
     * @return string
     */
    protected function _getParam($paramName, $path)
    {
        $pathParts = explode('/', $path);
        array_shift($pathParts);
        $fieldId = array_pop($pathParts);
        $firstGroupId = array_shift($pathParts);
        if (!isset($_FILES['groups'][$paramName])) {
            return null;
        }
        $groupData = $_FILES['groups'][$paramName];
        if (isset($groupData[$firstGroupId])) {
            $groupData = $groupData[$firstGroupId];
        }
        foreach ($pathParts as $groupId) {
            if (isset($groupData['groups'][$groupId])) {
                $groupData =  $groupData['groups'][$groupId];
            } else {
                return null;
            }
        }
        if (isset($groupData['fields'][$fieldId]['value'])) {
            return $groupData['fields'][$fieldId]['value'];
        }
        return null;
    }
}
