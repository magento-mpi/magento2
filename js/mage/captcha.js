var Captcha = {
    refresh: function(url, blockType, width, height, formId, isAdmin) {
        new Ajax.Request(url, {
            onSuccess: function (response) {
                if (response.responseText.isJSON()) {
                    var json = response.responseText.evalJSON();
                    if (!json.error && json.imgSrc) {
                        $(formId).writeAttribute('src', json.imgSrc);
                    }
                }
            },
            method: 'post',
            parameters: {
                'blockType': blockType,
                'width'    : width,
                'height'   : height,
                'formId'   : formId,
                'isAdmin'  : isAdmin
            }
        });
    }
};
