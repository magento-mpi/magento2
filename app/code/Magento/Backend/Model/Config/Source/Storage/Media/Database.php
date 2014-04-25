<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generate options for media database selection
 */
namespace Magento\Backend\Model\Config\Source\Storage\Media;

use Magento\Framework\App\Arguments;

class Database implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Arguments
     */
    protected $_arguments;

    /**
     * @param Arguments $arguments
     */
    public function __construct(Arguments $arguments)
    {
        $this->_arguments = $arguments;
    }

    /**
     * Returns list of available resources
     *
     * @return array
     */
    public function toOptionArray()
    {
        $resourceOptions = array();
        foreach (array_keys($this->_arguments->getResources()) as $resourceName) {
            $resourceOptions[] = array('value' => $resourceName, 'label' => $resourceName);
        }
        sort($resourceOptions);
        reset($resourceOptions);
        return $resourceOptions;
    }
}
