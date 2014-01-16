<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install config
 *
 * @category   Magento
 * @package    Magento_Install
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
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Config\Data $dataStorage
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Install\Model\Config\Data  $dataStorage,
        \Magento\Filesystem                 $filesystem
    ) {
        $this->_dataStorage = $dataStorage;
        $this->filesystem   = $filesystem;
    }

    /**
     * Get array of wizard steps
     *
     * array($index => \Magento\Object)
     *
     * @return array
     */
    public function getWizardSteps()
    {
        $data = $this->_dataStorage->get();
        $steps = array();
        foreach ($data['steps'] as $step) {
            $stepObject = new \Magento\Object($step);
            $steps[] = $stepObject;
        }
        return $steps;
    }

    /**
     * Retrieve writable path for checking
     *
     * array(
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

        $items = (isset($data['filesystem_prerequisites'])
            && isset($data['filesystem_prerequisites']['writables'])) ?
            $data['filesystem_prerequisites']['writables'] : array();

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
        $items = (isset($data['filesystem_prerequisites'])
            && isset($data['filesystem_prerequisites']['writables'])) ?
            $data['filesystem_prerequisites']['writables'] : array();
        foreach ($items as $nodeKey => $item) {
            $value = $item;
            $value['path'] = $this->filesystem->getPath($nodeKey);
            $paths[$nodeKey] = $value;
        }

        return $paths;
    }
}
