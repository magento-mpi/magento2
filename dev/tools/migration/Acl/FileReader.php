<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_Acl_FileReader
{
    /**
     * File path to json encoded file with map of acl xpath to acl identifier
     * @var string
     */
    protected $_filePath;

    protected $_resourcePrefix = 'config/acl/resources/admin/';

    public function __construct(array $data = array())
    {
        $this->_filePath = isset($data['filePath'])
            ? $data['filePath']
            : realpath(__DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'AclXPathToAclId.log');
    }

    /**
     * @static
     * @return Tools_Migration_Acl_FileReader
     */
    public static function getInstance()
    {
        return new Tools_Migration_Acl_FileReader();
    }

    /**
     * @return array
     */
    public function getAclIdentifiersMap()
    {
        $map = array();
        if (is_file($this->_filePath)) {
            $map = json_decode(file_get_contents($this->_filePath), true);
        }

        return $map;
    }
}
