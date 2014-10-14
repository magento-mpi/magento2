<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install config
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Model;

class Config
{
    /**
     * Config data model
     *
     * @var  \Magento\Install\Model\Config\Data
     */
    protected $_dataStorage;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Config\Data $dataStorage
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(\Magento\Install\Model\Config\Data $dataStorage, \Magento\Framework\Filesystem $filesystem)
    {
        $this->_dataStorage = $dataStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * Get array of wizard steps
     *
     * Array($index => \Magento\Framework\Object)
     *
     * @return array
     */
    public function getWizardSteps()
    {
        $data = $this->_dataStorage->get();
        $steps = array();
        foreach ($data['steps'] as $step) {
            $stepObject = new \Magento\Framework\Object($step);
            $steps[] = $stepObject;
        }
        return $steps;
    }

    /**
     * Retrieve writable path for checking
     *
     * Array(
     *      ['writeable'] => array(
     *          [$index] => array(
     *              ['path']
     *              ['recursive']
     *          )
     *      )
     * )
     *
     * @deprecated since 1.7.1.0
     *
     * @return array
     */
    public function getPathForCheck()
    {
        $data = $this->_dataStorage->get();
        $res = array();

        $items = isset(
            $data['filesystem_prerequisites']
        ) && isset(
            $data['filesystem_prerequisites']['writables']
        ) ? $data['filesystem_prerequisites']['writables'] : array();

        foreach ($items as $item) {
            $res['writeable'][] = $item;
        }

        return $res;
    }

    /**
     * Retrieve writable full paths for checking
     *
     * @return array
     */
    public function getWritableFullPathsForCheck()
    {
        $data = $this->_dataStorage->get();
        $paths = array();
        $items = isset(
            $data['filesystem_prerequisites']
        ) && isset(
            $data['filesystem_prerequisites']['writables']
        ) ? $data['filesystem_prerequisites']['writables'] : array();
        foreach ($items as $nodeKey => $item) {
            $value = $item;
            $value['path'] = $this->filesystem->getDirectoryWrite($nodeKey)->getAbsolutePath();
            $paths[$nodeKey] = $value;
        }

        return $paths;
    }
}
