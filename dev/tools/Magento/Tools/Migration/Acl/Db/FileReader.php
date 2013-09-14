<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_Acl_Db_FileReader
{
    /**
     * Extract resource id map from provided file
     *
     * @param string $fileName
     * @return array
     * @throws InvalidArgumentException
     */
    public function extractData($fileName)
    {
        if (empty($fileName)) {
            throw new InvalidArgumentException('Please specify correct name of a file that contains identifier map');
        }
        if (false == file_exists($fileName)) {
            throw new InvalidArgumentException('Provided identifier map file (' . $fileName . ') doesn\'t exist');
        }
        $data = json_decode(file_get_contents($fileName), true);

        $output = array();
        foreach ($data as $key => $value) {
            $newKey = str_replace('config/acl/resources/', '', $key);
            $output[$newKey] = $value;
        }
        return $output;
    }
}
