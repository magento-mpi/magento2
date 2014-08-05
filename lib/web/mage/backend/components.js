/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define([], function() {
    var components = {
        categoryForm: 'Magento_Catalog/catalog/category/form',
        newCategoryDialog: 'Magento_Catalog/js/new-category-dialog',
        requireCookie: 'Magento_Core/js/require-cookie',

        addressTabs: 'Magento_Customer/edit/tab/js/addresses',
        dataItemDeleteButton: 'Magento_Customer/edit/tab/js/addresses',
        groupedProduct: 'Magento_GroupedProduct/js/grouped-product',
        observableInputs: 'Magento_Customer/edit/tab/js/addresses',
        translateInline: 'mage/translate-inline',

        //Backend\view\adminhtml\templates\page\js\components.phtml
        form: 'mage/backend/form',
        button: 'mage/backend/button',
        accordion: 'mage/accordion',
        actionLink: 'mage/backend/action-link',
        validation: 'mage/backend/validation',
        notification: 'mage/backend/notification',
        loader: 'mage/loader_old',
        loaderAjax: 'mage/loader_old',
        floatingHeader: 'mage/backend/floating-header',
        suggest: 'mage/backend/suggest',
        mediabrowser: 'jquery/jstree/jquery.jstree',
        rolesTree: 'Magento_User/js/roles-tree',
        folderTree: 'Magento_Cms/js/folder-tree',
        categoryTree: 'Magento_Catalog/js/category-tree',
        tabs: 'mage/backend/tabs',
        treeSuggest: 'mage/backend/tree-suggest',
        baseImage: 'baseImage',

        //DesignEditor\view\adminhtml\templates\editor\toolbar\buttons\edit
        'vde-edit-button': 'Magento_DesignEditor/js/theme-revert',

        //Sales\view\adminhtml\templates\page\js\components.phtml
        orderEditDialog: 'Magento_Sales/order/edit/message',

        variationsAttributes: 'Magento_ConfigurableProduct/catalog/product-variation',
        calendar: 'mage/calendar',
        productGallery: 'Magento_Catalog/js/product-gallery',
        configurableAttribute: 'Magento_ConfigurableProduct/catalog/product/attribute',
        systemMessageDialog: 'Magento_AdminNotification/system/notification',
        fptAttribute: 'Magento_Weee/js/fpt-attribute',
        dropdown: 'mage/dropdown_old',
        collapsable: 'js/theme',
        collapsible: 'mage/collapsible',
        menu: 'mage/backend/menu',
        themeEdit: 'Magento_DesignEditor/js/theme-edit',
        integration: 'Magento_Integration/js/integration'
    };

    return components;
});