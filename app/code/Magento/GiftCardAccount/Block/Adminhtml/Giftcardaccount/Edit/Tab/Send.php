<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Init form fields
     *
     * @return Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send
     */
    public function initForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_send');

        $model = $this->_coreRegistry->registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => __('Send Gift Card'))
        );

        $fieldset->addField('recipient_email', 'text', array(
            'label'     => __('Recipient Email'),
            'title'     => __('Recipient Email'),
            'class'     => 'validate-email',
            'name'      => 'recipient_email',
        ));

        $fieldset->addField('recipient_name', 'text', array(
            'label'     => __('Recipient Name'),
            'title'     => __('Recipient Name'),
            'name'      => 'recipient_name',
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'     => 'recipient_store',
                'label'    => __('Send Email from the Following Store View'),
                'title'    => __('Send Email from the Following Store View'),
                'after_element_html' => $this->_getStoreIdScript()
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('action', 'hidden', array(
            'name'      => 'send_action',
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }

    protected function _getStoreIdScript()
    {
        $websiteStores = array();
        foreach ($this->_storeManager->getWebsites() as $websiteId => $website) {
            $websiteStores[$websiteId] = array();
            foreach ($website->getGroups() as $groupId => $group) {
                $websiteStores[$websiteId][$groupId] = array(
                    'name' => $group->getName()
                );
                foreach ($group->getStores() as $storeId => $store) {
                    $websiteStores[$websiteId][$groupId]['stores'][] = array(
                        'id'   => $storeId,
                        'name' => $store->getName(),
                    );
                }
            }
        }

        $websiteStores = $this->_coreData->jsonEncode($websiteStores);

        $result  = '<script type="text/javascript">//<![CDATA[' . "\n";
        $result .= "var websiteStores = $websiteStores;";
        $result .= "Event.observe('_infowebsite_id', 'change', setCurrentStores);";
        $result .= "setCurrentStores();";
        $result .= 'function setCurrentStores(){
            var wSel = $("_infowebsite_id");
            var sSel = $("_sendstore_id");

            sSel.innerHTML = \'\';
            var website = wSel.options[wSel.selectedIndex].value;
            if (websiteStores[website]) {
                groups = websiteStores[website];
                for (groupKey in groups) {
                    group = groups[groupKey];
                    optionGroup = document.createElement("OPTGROUP");
                    optionGroup.label = group["name"];
                    sSel.appendChild(optionGroup);

                    stores = group["stores"];
                    for (i=0; i < stores.length; i++) {
                        var option = document.createElement("option");
                        option.appendChild(document.createTextNode(stores[i]["name"]));
                        option.setAttribute("value", stores[i]["id"]);
                        optionGroup.appendChild(option);
                    }
                }
            }
            else {
              var option = document.createElement("option");
              option.appendChild(document.createTextNode(\''.__('-- First Please Select a Website --').'\'));
              sSel.appendChild(option);
            }
        }
        //]]></script>';

        return $result;
    }
}
