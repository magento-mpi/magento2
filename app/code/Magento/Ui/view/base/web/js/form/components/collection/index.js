define([
    'Magento_Ui/js/initializer/collection',
    './collection',
    './item'
], function (Collection, FormCollection, Item) {
    return Collection(Item, { use: FormCollection });
});