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
     * Filename of the entry point script
     *
     * @var string
     */
    private $_entryPoint;

    /**
     * @param string $baseDir
     * @param array $params
     */
    public function __construct($baseDir, array $params = array())
    {
        $this->_entryPoint = $params['entryPoint'];
        unset($params['entryPoint']);

        parent::__construct(new Mage_Core_Model_Config_Primary($baseDir, $params));
    }

    /**
     * Init object manager, configuring it with additional parameters
     */
    protected function _initObjectManager()
    {
        parent::_initObjectManager();

        $this->_objectManager->configure(array(
            'Mage_Index_Model_Shell' => array(
                'parameters' => array(
                    'entryPoint' => $this->_entryPoint,
                )
            )
        ));
    }

    /**
     * Process request to application
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _processRequest()
    {
        /** @var $shell Mage_Index_Model_Shell */
        $shell = $this->_objectManager->create('Mage_Index_Model_Shell');
        $shell->run();
        if ($shell->hasErrors()) {
            exit(1);
        }
    }
}
