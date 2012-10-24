var varientCategoryForm = Class.create();
varientCategoryForm.prototype = Object.extend(new varienForm(), {
    submit: function (url) {
        this.errorSections = $H({});
        this.canShowError = true;
        this.submitUrl = url;
        if (this.validator && this.validator.validate()) {
            if(this.validationUrl){
                this._validate();
            }
            else{
                if (this.isSubmitted) {
                    return false;
                }
                this.isSubmitted = true;
                this._submit();
            }
            displayLoadingMask();
            return true;
        }
        return false;
    },

    refreshPath: function () {
        var categoryId = this.getCategoryId();

        if (!categoryId) {
            return false;
        }

        var refreshPathSuccess = function(transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON();
                if (response.error) {
                    alert(response.message);
                } else {
                    if (this.getCategoryId() == response.id) {
                        this.setCategoryPath(response.path);
                    }
                }
            }
        };

        new Ajax.Request(
                this.refreshUrl,
                {
                    method:     'POST',
                    evalScripts: true,
                    onSuccess: refreshPathSuccess.bind(this)
                }
        );
    },

    getCategoryId: function() {
        var collection = $(this.formId).getInputs('hidden','general[id]');
        if (collection.size() > 0) {
            return collection.first().value;
        }
        return false;
    },

    setCategoryPath: function(path) {
        var collection = $(this.formId).getInputs('hidden','general[path]');
        if (collection.size() > 0) {
            return collection.first().value = path;
        }
    }
});


/**
* Create/edit some category
*/
function categorySubmit(url, useAjax) {
    var activeTab = $('active_tab_id');
    if (activeTab) {
        if (activeTab.tabsJsObject && activeTab.tabsJsObject.activeTab) {
            activeTab.value = activeTab.tabsJsObject.activeTab.id;
        }
    }

    var params = {};
    var fields = $('category_edit_form').getElementsBySelector('input', 'select');
    for(var i=0;i<fields.length;i++){
        if (!fields[i].name) {
            continue;
        }
        params[fields[i].name] = fields[i].getValue();
    }

    // Get info about what we're submitting - to properly update tree nodes
    var categoryId = params['general[id]'] ? params['general[id]'] : 0;
    var isCreating = categoryId == 0; // Separate variable is needed because '0' in javascript converts to TRUE
    var path = params['general[path]'].split('/');
    var parentId = path.pop();
    if (parentId == categoryId) { // Maybe path includes category id itself
        parentId = path.pop();
    }

    // Make operations with category tree
    if (isCreating) {
        /* Some specific tasks for creating category */
        if (!tree.currentNodeId) {
            // First submit of form - select some node to be current
            tree.currentNodeId = parentId;
        }
        tree.addNodeTo = parentId;
    } else {
        /* Some specific tasks for editing category */
        // Maybe change category enabled/disabled style
        if (tree && tree.storeId==0) {
            var currentNode = tree.getNodeById(categoryId);

            if (currentNode) {
                if (parseInt(params['general[is_active]'])) {
                    var oldClass = 'no-active-category';
                    var newClass = 'active-category';
                } else {
                    var oldClass = 'active-category';
                    var newClass = 'no-active-category';
                }

                Element.removeClassName(currentNode.ui.wrap.firstChild, oldClass);
                Element.addClassName(currentNode.ui.wrap.firstChild, newClass);
            }
        }
    }

    // Submit form
    categoryForm.submit();
}
