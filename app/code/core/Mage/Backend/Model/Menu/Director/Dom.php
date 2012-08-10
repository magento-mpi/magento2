<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Director_Dom extends Mage_Backend_Model_Menu_DirectorAbstract
{
    /**
     * Extracted config data
     * @var array
     */
    protected $_extractedData = array();

    /**
     * @var Mage_Backend_Model_Menu_Logger
     */
    protected $_logger;

    /**
     * @param DOMDocument $menuConfig
     * @param Magento_ObjectManager $factory
     * @param Mage_Backend_Model_Menu_Logger $menuLogger
     */
    public function __construct(
        DOMDocument $menuConfig,
        Magento_ObjectManager $factory,
        Mage_Backend_Model_Menu_Logger $menuLogger
    ) {
        parent::__construct($menuConfig, $factory);
        $this->_logger = $menuLogger;
        $this->_extractData();
    }

    /**
     * Extract data from DOMDocument
     * @return Mage_Backend_Model_Menu_Director_Dom
     */
    protected function _extractData()
    {
        $attributeNamesList = array(
            'id',
            'title',
            'toolTip',
            'module',
            'sortOrder',
            'action',
            'parent',
            'resource',
            'dependsOnModule',
            'dependsOnConfig',
        );
        $xpath = new DOMXPath($this->_configModel);
        $nodeList = $xpath->query('/config/menu/*');
        for ($i = 0; $i < $nodeList->length; $i++) {
            $item = array();
            $node = $nodeList->item($i);
            $item['type'] = $node->nodeName;
            foreach ($attributeNamesList as $name) {
                if ($node->hasAttribute($name)) {
                    $item[$name] = $node->getAttribute($name);
                }
            }
            $this->_extractedData[] = $item;
        }
    }

    /**
     * Get data that were extracted from config storage
     * @return array
     */
    public function getExtractedData()
    {
        return $this->_extractedData;
    }

    /**
     * Get command object
     * @param array $data command params
     * @return Mage_Backend_Model_Menu_Builder_CommandAbstract
     */
    protected function _getCommand($data)
    {
        switch ($data['type']) {
            case 'update':
                $command = $this->_factory->create(
                    'Mage_Backend_Model_Menu_Builder_Command_Update',
                    array('data' => $data)
                );
                $this->_logger->log(sprintf('Update on item with id %s was processed', $command->getId()));
                break;

            case 'remove':
                $command = $this->_factory->create(
                    'Mage_Backend_Model_Menu_Builder_Command_Remove',
                    array('data' => $data)
                );
                $this->_logger->log(sprintf('Remove on item with id %s was processed', $command->getId()));
                break;

            default:
                $command = $this->_factory->create(
                    'Mage_Backend_Model_Menu_Builder_Command_Add',
                    array('data' => $data)
                );
                break;
        }
        return $command;
    }

    /**
     *
     * @param Mage_Backend_Model_Menu_Builder $builder
     * @throws InvalidArgumentException if invalid builder object
     * @return Mage_Backend_Model_Menu_DirectorAbstract
     */
    public function buildMenu(Mage_Backend_Model_Menu_Builder $builder)
    {
        foreach ($this->getExtractedData() as $data) {
            $command = $this->_getCommand($data);
            $builder->processCommand($command);
        }
        return $this;
    }
}
