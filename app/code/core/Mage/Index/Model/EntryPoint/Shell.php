<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Model_EntryPoint_Shell extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * @param string $baseDir
     * @param array $params
     */
    public function __construct($baseDir, array $params = array())
    {
        $entryPoint = $params['entryPoint'];
        unset($params['entryPoint']);
        $config = new Mage_Core_Model_Config_Primary($baseDir, $params);
        parent::__construct($config);
        $this->_objectManager->configure(array(
            'Mage_Index_Model_Shell' => array(
                'parameters' => array(
                    'entryPoint' => $entryPoint,
                )
            )
        ));
    }

    /**
     * Process request to application
     */
    public function processRequest()
    {
        /** @var $shell Mage_Index_Model_Shell */
        $shell = $this->_objectManager->create('Mage_Index_Model_Shell');
        $shell->run();
        if ($shell->hasErrors()) {
            exit(1);
        }
    }
}
