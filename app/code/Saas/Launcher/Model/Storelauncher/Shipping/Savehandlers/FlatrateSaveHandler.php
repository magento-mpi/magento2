<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat Rate configuration save handler
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Validator_Float
     */
    protected $_validator;

    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Validator_Float $validator
     */
    public function __construct(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Validator_Float $validator
    ) {
        parent::__construct($config, $backendConfigModel);
        $this->_locale = $locale;
        $validator->setLocale($this->_locale->getLocale());
        $this->_validator = $validator;
    }

    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('carriers');
    }

    /**
     * Prepare configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (empty($data['groups']['flatrate']['fields']['name']['value'])) {
            throw new Saas_Launcher_Exception('Display Name is required.');
        }
        if (!isset($data['groups']['flatrate']['fields']['price']['value'])) {
            throw new Saas_Launcher_Exception('Price is required.');
        }
        $rate = trim($data['groups']['flatrate']['fields']['price']['value']);
        if (!$this->_validator->isValid($rate) || $this->_locale->getNumber($rate) < 0) {
            throw new Saas_Launcher_Exception('Please enter a number 0 or greater in the Price field.');
        }
        if (!isset($data['groups']['flatrate']['fields']['type']['value'])
            || !in_array($data['groups']['flatrate']['fields']['type']['value'], array('', 'O', 'I'))
        ) {
            throw new Saas_Launcher_Exception('Type is required.');
        }

        $preparedData['carriers']['flatrate']['fields']['name']['value'] =
            trim($data['groups']['flatrate']['fields']['name']['value']);
        $preparedData['carriers']['flatrate']['fields']['price']['value'] = $this->_locale->getNumber($rate);
        $preparedData['carriers']['flatrate']['fields']['type']['value'] =
            trim($data['groups']['flatrate']['fields']['type']['value']);

        // Enable Flat Rate for checkout
        $preparedData['carriers']['flatrate']['fields']['active']['value'] = 1;

        return $preparedData;
    }
}
