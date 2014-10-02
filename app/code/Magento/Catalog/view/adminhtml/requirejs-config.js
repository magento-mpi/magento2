/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

var config = {
    map: {
        '*': {
            categoryForm:       'Magento_Catalog/catalog/category/form',
            newCategoryDialog:  'Magento_Catalog/js/new-category-dialog',
            categoryTree:       'Magento_Catalog/js/category-tree',
            productGallery:     'Magento_Catalog/js/product-gallery'
        }
    },
    paths: {
        baseImage: 'Magento_Catalog/catalog/base-image-uploader'
    },
    deps: [
        "Magento_Catalog/catalog/product"
    ]
};