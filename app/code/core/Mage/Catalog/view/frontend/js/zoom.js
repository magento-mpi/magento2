/**
 * {license_notice}
 *
 * @category    frontend poll
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true*/
(function ($) {
    $(document).ready(function() {
        // Default zoom variables
        var _zoomInit = {
            imageSelector: '#image',
            sliderSelector: '#slider',
            sliderSpeed: 10,
            zoomNoticeSelector: '#track_hint',
            zoomInSelector: '#zoom_in',
            zoomOutSelector: '#zoom_out'
        };
        $.mage.event.trigger("mage.zoom.initialize", _zoomInit);
        var _slider, _intervalId = null;
        var _sliderMax = $(_zoomInit.sliderSelector).width();
        var _image = $(_zoomInit.imageSelector);
        var _imageWidth = _image.width();
        var _imageHeight = _image.height();
        var _imageParent = _image.parent();
        var _imageParentWidth = _imageParent.width();
        var _imageParentHeight = _imageParent.height();
        var _ceilingZoom, _imageInitTop, _imageInitLeft;
        var _showFullImage = false;

        // Image is small than parent container, no need to see full picutre or zoom slider
        if (_imageWidth < _imageParentWidth && _imageHeight < _imageParentHeight) {
            $(_zoomInit.sliderSelector).parent().hide();
            $(_zoomInit.zoomNoticeSelector).hide();
            return;
        }
        // Resize Image to fit parent container
        if (_imageWidth > _imageHeight){
            _ceilingZoom = _imageWidth / _imageParentWidth;
            _image.width(_imageParentWidth);
            _image.css('top', ((_imageParentHeight - _image.height()) / 2) + 'px');
        } else {
            _ceilingZoom = _imageHeight / _imageParentHeight;
            _image.height(_imageParentHeight);
            _image.css('left', ((_imageParentWidth - _image.width()) / 2) + 'px');
        }
        _imageInitTop = _image.position().top;
        _imageInitLeft = _image.position().left;
        // Make Image Draggable
        function _draggableImage(){
            var _topX = _image.offset().left,
                _topY = _image.offset().top,
                _bottomX = _image.offset().left,
                _bottomY = _image.offset().top;
            // Calculate x offset if image width is greater than image container width
            if (_image.width() > _imageParentWidth) {
                _topX = _image.width() - (_imageParent.offset().left - _image.offset().left) - _imageParentWidth;
                _topX = _image.offset().left - _topX;
                _bottomX = _imageParent.offset().left - _image.offset().left;
                _bottomX = _image.offset().left + _bottomX;
            }
            // Calculate y offset if image height is greater than image container height
            if (_image.height() > _imageParentHeight) {
                _topY = _image.height() - (_imageParent.offset().top - _image.offset().top) - _imageParentHeight;
                _topY = _image.offset().top - _topY;
                _bottomY = _imageParent.offset().top - _image.offset().top;
                _bottomY = _image.offset().top + _bottomY;
            }
            $(_zoomInit.imageSelector).draggable({
                containment: [_topX, _topY, _bottomX, _bottomY],
                scroll: false
            });
        }
        // Image zooming bases on slider position
        function _zoom(_sliderPosition, _sliderLength){
            var _ratio = _sliderPosition / _sliderLength;
            _ratio = _ratio > 1 ? 1 : _ratio;
            var _imageOldLeft = _image.position().left;
            var _imageOldTop = _image.position().top;
            var _imageOldWidth = _image.width();
            var _imageOldHeight = _image.height();
            var _overSize = (_imageWidth > _imageParentWidth || _imageHeight > _imageParentHeight);
            var _floorZoom = 1, _imageZoom = _floorZoom + (_ratio * (_ceilingZoom - _floorZoom));
            if (_overSize) {
                if (_imageWidth > _imageHeight) {
                    _image.width(_imageZoom * _imageParentWidth);
                } else {
                    _image.height(_imageZoom * _imageParentHeight);
                }
            } else {
                $(_zoomInit.sliderSelector).hide();
            }
            var _imageNewLeft = _imageOldLeft - (_image.width() - _imageOldWidth) / 2;
            var _imageNewTop = _imageOldTop - (_image.height() - _imageOldHeight) / 2;
            // Image can't be positioned more left than original left
            if (_imageNewLeft > _imageInitLeft || _image.width() < _imageParentWidth) {
                _imageNewLeft = _imageInitLeft;
            }
            // Image can't be positioned more right than the difference between parent width and image current width
            if (Math.abs(_imageNewLeft) > Math.abs(_imageParentWidth - _image.width())) {
                _imageNewLeft = _imageParentWidth - _image.width();
            }
            // Image can't be positioned more down than original top
            if (_imageNewTop > _imageInitTop || _image.height() < _imageParentHeight) {
                _imageNewTop = _imageInitTop;
            }
            // Image can't be positioned more top than the difference between parent height and image current height
            if (Math.abs(_imageNewTop) > Math.abs(_imageParentHeight - _image.height())) {
                _imageNewTop = _imageParentHeight - _image.height();
            }
            _image.css('left', _imageNewLeft + 'px');
            _image.css('top', _imageNewTop + 'px');
            _draggableImage();
        }
        // Slide slider to zoom in or out the picture
        _slider = $(_zoomInit.sliderSelector).slider({
            value:0,
            min: 0,
            max: _sliderMax,
            slide: function(event, ui){
                _zoom(ui.value, _sliderMax);
            },
            change: function(event, ui){
                _zoom(ui.value, _sliderMax);
            }
        });
        // Mousedown on zoom in icon to zoom in picture
        $(_zoomInit.zoomInSelector).on('mousedown', function() {
            _intervalId = setInterval(function() {
                _slider.slider('value', _slider.slider('value') + 1);
            }, _zoomInit.sliderSpeed);
        }).on('mouseup mouseleave', function() {
            clearInterval(_intervalId);
        });
        // Mousedown on zoom out icon to zoom out picture
        $(_zoomInit.zoomOutSelector).on('mousedown', function() {
            _intervalId = setInterval(function() {
                _slider.slider('value', _slider.slider('value') - 1);
            }, _zoomInit.sliderSpeed);
        }).on('mouseup mouseleave', function() {
            clearInterval(_intervalId);
        });
        // Double-click image to see full picture
        $(_zoomInit.imageSelector).on('dblclick', function() {
            _showFullImage = !_showFullImage;
            var ratio = _showFullImage ? _sliderMax : _slider.slider('value');
            _zoom(ratio, _sliderMax);

            if (_showFullImage){
                $(_zoomInit.sliderSelector).hide();
                $(_zoomInit.zoomInSelector).hide();
                $(_zoomInit.zoomOutSelector).hide();
                _imageParent.css('overflow', 'visible');
                _imageParent.css('zIndex', '1000');
            } else {
                $(_zoomInit.sliderSelector).show();
                $(_zoomInit.zoomInSelector).show();
                $(_zoomInit.zoomOutSelector).show();
                _imageParent.css('overflow', 'hidden');
                _imageParent.css('zIndex', '9');
            }
        });
        // Window resize will change offset for draggable
        $(window).resize(_draggableImage);
    });
}(jQuery));