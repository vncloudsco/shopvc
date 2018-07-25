/*!
 * WooCommerce Variation Swatches v1.0.31 
 * 
 * Author: Emran Ahmed ( emran.bd.08@gmail.com ) 
 * Date: 2018-7-16 20:51:28
 * Released under the GPLv3 license.
 */
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
__webpack_require__(3);
__webpack_require__(4);
__webpack_require__(5);
__webpack_require__(6);
module.exports = __webpack_require__(7);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

jQuery(function ($) {
    Promise.resolve().then(function () {
        return __webpack_require__(2);
    }).then(function (_ref) {
        var PluginHelper = _ref.PluginHelper;

        PluginHelper.SelectWoo();
        PluginHelper.ColorPicker();
        PluginHelper.FieldDependency();
        PluginHelper.ImageUploader();
        PluginHelper.AttributeDialog();
        $(document.body).on('woocommerce_added_attribute', function () {
            PluginHelper.SelectWoo();
            PluginHelper.ColorPicker();
            PluginHelper.ImageUploader();
            PluginHelper.AttributeDialog();
        });

        $(document.body).on('wvs_pro_product_swatches_variation_loaded', function () {
            PluginHelper.ColorPicker();
            PluginHelper.ImageUploader();
        });

        $('.gwp-live-feed-close').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('feed_id');
            wp.ajax.send('gwp_live_feed_close', {
                data: { id: id }
            });

            $(this).parent().fadeOut('fast', function () {
                $(this).remove();
            });
        });
    });
}); // end of jquery main wrapper

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PluginHelper", function() { return PluginHelper; });
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/*global WVSPluginObject, wp, woocommerce_admin_meta_boxes*/

var PluginHelper = function ($) {
    var PluginHelper = function () {
        function PluginHelper() {
            _classCallCheck(this, PluginHelper);
        }

        _createClass(PluginHelper, null, [{
            key: 'ImageUploader',
            value: function ImageUploader() {
                $(document).off('click', 'button.wvs_upload_image_button');
                $(document).on('click', 'button.wvs_upload_image_button', this.AddImage);
                $(document).on('click', 'button.wvs_remove_image_button', this.RemoveImage);
            }
        }, {
            key: 'AddImage',
            value: function AddImage(event) {
                var _this = this;

                event.preventDefault();
                event.stopPropagation();

                var file_frame = void 0;

                if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {

                    // If the media frame already exists, reopen it.
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.select_image = wp.media({
                        title: WVSPluginObject.media_title,
                        button: {
                            text: WVSPluginObject.button_title
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        if ($.trim(attachment.id) !== '') {

                            var url = typeof attachment.sizes.thumbnail === 'undefined' ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                            $(_this).prev().val(attachment.id);
                            $(_this).closest('.meta-image-field-wrapper').find('img').attr('src', url);
                            $(_this).next().show();
                        }
                        //file_frame.close();
                    });

                    // When open select selected
                    file_frame.on('open', function () {

                        // Grab our attachment selection and construct a JSON representation of the model.
                        var selection = file_frame.state().get('selection');
                        var current = $(_this).prev().val();
                        var attachment = wp.media.attachment(current);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });

                    // Finally, open the modal.
                    file_frame.open();
                }
            }
        }, {
            key: 'RemoveImage',
            value: function RemoveImage(event) {

                event.preventDefault();
                event.stopPropagation();

                var placeholder = $(this).closest('.meta-image-field-wrapper').find('img').data('placeholder');
                $(this).closest('.meta-image-field-wrapper').find('img').attr('src', placeholder);
                $(this).prev().prev().val('');
                $(this).hide();
                return false;
            }
        }, {
            key: 'SelectWoo',
            value: function SelectWoo() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'select.wvs-selectwoo';

                if ($().selectWoo) {
                    $(selector).selectWoo({
                        allowClear: true
                    });
                }
            }
        }, {
            key: 'ColorPicker',
            value: function ColorPicker() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'input.wvs-color-picker';

                if ($().wpColorPicker) {
                    $(selector).wpColorPicker();
                }
            }
        }, {
            key: 'FieldDependency',
            value: function FieldDependency() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '[data-depends]';

                if ($().FormFieldDependency) {
                    $(selector).FormFieldDependency();
                }
            }
        }, {
            key: 'savingDialog',
            value: function savingDialog($wrapper, $dialog, taxonomy) {

                var data = {};
                var term = '';

                // @TODO: We should use form data, because we have to pick array based data also :)

                $dialog.find('input, select').each(function () {
                    var key = $(this).attr('name');
                    var value = $(this).val();
                    if (key) {
                        if (key === 'tag_name') {
                            term = value;
                        } else {
                            data[key] = value;
                        }
                        $(this).val('');
                    }
                });

                if (term) {
                    $('.product_attributes').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    var ajax_data = _extends({
                        action: 'woocommerce_add_new_attribute',
                        taxonomy: taxonomy,
                        term: term,
                        security: woocommerce_admin_meta_boxes.add_attribute_nonce
                    }, data);

                    $.post(woocommerce_admin_meta_boxes.ajax_url, ajax_data, function (response) {

                        if (response.error) {
                            // Error.
                            window.alert(response.error);
                        } else if (response.slug) {
                            // Success.
                            $wrapper.find('select.attribute_values').append('<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>');
                            $wrapper.find('select.attribute_values').change();
                        }

                        $('.product_attributes').unblock();
                    });
                } else {
                    $('.product_attributes').unblock();
                }
            }
        }, {
            key: 'AttributeDialog',
            value: function AttributeDialog() {

                var self = this;
                $('.product_attributes').on('click', 'button.wvs_add_new_attribute', function (event) {

                    event.preventDefault();

                    var $wrapper = $(this).closest('.woocommerce_attribute');
                    var attribute = $wrapper.data('taxonomy');
                    var title = $(this).data('dialog_title');

                    $('.wvs-attribute-dialog-for-' + attribute).dialog({
                        title: '',
                        dialogClass: 'wp-dialog wvs-attribute-dialog',
                        classes: {
                            "ui-dialog": "wp-dialog wvs-attribute-dialog"
                        },
                        autoOpen: false,
                        draggable: true,
                        width: 'auto',
                        modal: true,
                        resizable: false,
                        closeOnEscape: true,
                        position: {
                            my: "center",
                            at: "center",
                            of: window
                        },
                        open: function open() {
                            // close dialog by clicking the overlay behind it
                            $('.ui-widget-overlay').bind('click', function () {
                                $('#attribute-dialog').dialog('close');
                            });
                        },
                        create: function create() {
                            // style fix for WordPress admin
                            // $('.ui-dialog-titlebar-close').addClass('ui-button');
                        }
                    }).dialog("option", "title", title).dialog("option", "buttons", [{
                        text: WVSPluginObject.dialog_save,
                        click: function click() {
                            self.savingDialog($wrapper, $(this), attribute);
                            $(this).dialog("close").dialog("destroy");
                        }
                    }, {
                        text: WVSPluginObject.dialog_cancel,
                        click: function click() {
                            $(this).dialog("close").dialog("destroy");
                        }
                    }]).dialog('open');
                });
            }
        }]);

        return PluginHelper;
    }();

    return PluginHelper;
}(jQuery);



/***/ }),
/* 3 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 4 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 5 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 6 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 7 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXNzZXRzL2pzL2FkbWluLmpzIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vL3dlYnBhY2svYm9vdHN0cmFwIGJmMmI2N2M0ZThjOGIzYzBiNmQxIiwid2VicGFjazovLy9zcmMvanMvYmFja2VuZC5qcyIsIndlYnBhY2s6Ly8vc3JjL2pzL1BsdWdpbkhlbHBlci5qcyIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9iYWNrZW5kLnNjc3M/YmU2MSIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9mcm9udGVuZC5zY3NzIiwid2VicGFjazovLy8uL3NyYy9zY3NzL3Rvb2x0aXAuc2NzcyIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy90aGVtZS1vdmVycmlkZS5zY3NzIiwid2VicGFjazovLy8uL3NyYy9zY3NzL2N1c3RvbWl6ZS1oZWFkaW5nLWNvbnRyb2wuc2NzcyJdLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAwKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCBiZjJiNjdjNGU4YzhiM2MwYjZkMSIsImpRdWVyeSgkID0+IHtcbiAgICBpbXBvcnQoJy4vUGx1Z2luSGVscGVyJykudGhlbigoe1BsdWdpbkhlbHBlcn0pID0+IHtcbiAgICAgICAgUGx1Z2luSGVscGVyLlNlbGVjdFdvbygpO1xuICAgICAgICBQbHVnaW5IZWxwZXIuQ29sb3JQaWNrZXIoKTtcbiAgICAgICAgUGx1Z2luSGVscGVyLkZpZWxkRGVwZW5kZW5jeSgpO1xuICAgICAgICBQbHVnaW5IZWxwZXIuSW1hZ2VVcGxvYWRlcigpO1xuICAgICAgICBQbHVnaW5IZWxwZXIuQXR0cmlidXRlRGlhbG9nKCk7XG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkub24oJ3dvb2NvbW1lcmNlX2FkZGVkX2F0dHJpYnV0ZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5TZWxlY3RXb28oKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5Db2xvclBpY2tlcigpO1xuICAgICAgICAgICAgUGx1Z2luSGVscGVyLkltYWdlVXBsb2FkZXIoKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5BdHRyaWJ1dGVEaWFsb2coKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJChkb2N1bWVudC5ib2R5KS5vbignd3ZzX3Byb19wcm9kdWN0X3N3YXRjaGVzX3ZhcmlhdGlvbl9sb2FkZWQnLCAoKSA9PiB7XG4gICAgICAgICAgICBQbHVnaW5IZWxwZXIuQ29sb3JQaWNrZXIoKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5JbWFnZVVwbG9hZGVyKCk7XG4gICAgICAgIH0pXG5cbiAgICAgICAgJCgnLmd3cC1saXZlLWZlZWQtY2xvc2UnKS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgbGV0IGlkID0gJCh0aGlzKS5kYXRhKCdmZWVkX2lkJyk7XG4gICAgICAgICAgICB3cC5hamF4LnNlbmQoJ2d3cF9saXZlX2ZlZWRfY2xvc2UnLCB7XG4gICAgICAgICAgICAgICAgZGF0YSA6IHtpZH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudCgpLmZhZGVPdXQoJ2Zhc3QnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfSk7XG4gICAgfSk7XG59KTsgIC8vIGVuZCBvZiBqcXVlcnkgbWFpbiB3cmFwcGVyXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHNyYy9qcy9iYWNrZW5kLmpzIiwiLypnbG9iYWwgV1ZTUGx1Z2luT2JqZWN0LCB3cCwgd29vY29tbWVyY2VfYWRtaW5fbWV0YV9ib3hlcyovXG5cbmNvbnN0IFBsdWdpbkhlbHBlciA9ICgoJCkgPT4ge1xuICAgIGNsYXNzIFBsdWdpbkhlbHBlciB7XG5cbiAgICAgICAgc3RhdGljIEltYWdlVXBsb2FkZXIoKSB7XG4gICAgICAgICAgICAkKGRvY3VtZW50KS5vZmYoJ2NsaWNrJywgJ2J1dHRvbi53dnNfdXBsb2FkX2ltYWdlX2J1dHRvbicpO1xuICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJ2J1dHRvbi53dnNfdXBsb2FkX2ltYWdlX2J1dHRvbicsIHRoaXMuQWRkSW1hZ2UpO1xuICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJ2J1dHRvbi53dnNfcmVtb3ZlX2ltYWdlX2J1dHRvbicsIHRoaXMuUmVtb3ZlSW1hZ2UpO1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIEFkZEltYWdlKGV2ZW50KSB7XG5cbiAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgbGV0IGZpbGVfZnJhbWU7XG5cbiAgICAgICAgICAgIGlmICh0eXBlb2Ygd3AgIT09ICd1bmRlZmluZWQnICYmIHdwLm1lZGlhICYmIHdwLm1lZGlhLmVkaXRvcikge1xuXG4gICAgICAgICAgICAgICAgLy8gSWYgdGhlIG1lZGlhIGZyYW1lIGFscmVhZHkgZXhpc3RzLCByZW9wZW4gaXQuXG4gICAgICAgICAgICAgICAgaWYgKGZpbGVfZnJhbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgZmlsZV9mcmFtZS5vcGVuKCk7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBDcmVhdGUgdGhlIG1lZGlhIGZyYW1lLlxuICAgICAgICAgICAgICAgIGZpbGVfZnJhbWUgPSB3cC5tZWRpYS5mcmFtZXMuc2VsZWN0X2ltYWdlID0gd3AubWVkaWEoe1xuICAgICAgICAgICAgICAgICAgICB0aXRsZSAgICA6IFdWU1BsdWdpbk9iamVjdC5tZWRpYV90aXRsZSxcbiAgICAgICAgICAgICAgICAgICAgYnV0dG9uICAgOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZXh0IDogV1ZTUGx1Z2luT2JqZWN0LmJ1dHRvbl90aXRsZVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBtdWx0aXBsZSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gV2hlbiBhbiBpbWFnZSBpcyBzZWxlY3RlZCwgcnVuIGEgY2FsbGJhY2suXG4gICAgICAgICAgICAgICAgZmlsZV9mcmFtZS5vbignc2VsZWN0JywgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBsZXQgYXR0YWNobWVudCA9IGZpbGVfZnJhbWUuc3RhdGUoKS5nZXQoJ3NlbGVjdGlvbicpLmZpcnN0KCkudG9KU09OKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCQudHJpbShhdHRhY2htZW50LmlkKSAhPT0gJycpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHVybCA9ICh0eXBlb2YoYXR0YWNobWVudC5zaXplcy50aHVtYm5haWwpID09PSAndW5kZWZpbmVkJykgPyBhdHRhY2htZW50LnNpemVzLmZ1bGwudXJsIDogYXR0YWNobWVudC5zaXplcy50aHVtYm5haWwudXJsO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnByZXYoKS52YWwoYXR0YWNobWVudC5pZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5tZXRhLWltYWdlLWZpZWxkLXdyYXBwZXInKS5maW5kKCdpbWcnKS5hdHRyKCdzcmMnLCB1cmwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5uZXh0KCkuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIC8vZmlsZV9mcmFtZS5jbG9zZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gV2hlbiBvcGVuIHNlbGVjdCBzZWxlY3RlZFxuICAgICAgICAgICAgICAgIGZpbGVfZnJhbWUub24oJ29wZW4nLCAoKSA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gR3JhYiBvdXIgYXR0YWNobWVudCBzZWxlY3Rpb24gYW5kIGNvbnN0cnVjdCBhIEpTT04gcmVwcmVzZW50YXRpb24gb2YgdGhlIG1vZGVsLlxuICAgICAgICAgICAgICAgICAgICBsZXQgc2VsZWN0aW9uICA9IGZpbGVfZnJhbWUuc3RhdGUoKS5nZXQoJ3NlbGVjdGlvbicpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgY3VycmVudCAgICA9ICQodGhpcykucHJldigpLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgYXR0YWNobWVudCA9IHdwLm1lZGlhLmF0dGFjaG1lbnQoY3VycmVudCk7XG4gICAgICAgICAgICAgICAgICAgIGF0dGFjaG1lbnQuZmV0Y2goKTtcbiAgICAgICAgICAgICAgICAgICAgc2VsZWN0aW9uLmFkZChhdHRhY2htZW50ID8gW2F0dGFjaG1lbnRdIDogW10pO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gRmluYWxseSwgb3BlbiB0aGUgbW9kYWwuXG4gICAgICAgICAgICAgICAgZmlsZV9mcmFtZS5vcGVuKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgUmVtb3ZlSW1hZ2UoZXZlbnQpIHtcblxuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBsZXQgcGxhY2Vob2xkZXIgPSAkKHRoaXMpLmNsb3Nlc3QoJy5tZXRhLWltYWdlLWZpZWxkLXdyYXBwZXInKS5maW5kKCdpbWcnKS5kYXRhKCdwbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcubWV0YS1pbWFnZS1maWVsZC13cmFwcGVyJykuZmluZCgnaW1nJykuYXR0cignc3JjJywgcGxhY2Vob2xkZXIpO1xuICAgICAgICAgICAgJCh0aGlzKS5wcmV2KCkucHJldigpLnZhbCgnJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBTZWxlY3RXb28oc2VsZWN0b3IgPSAnc2VsZWN0Lnd2cy1zZWxlY3R3b28nKSB7XG4gICAgICAgICAgICBpZiAoJCgpLnNlbGVjdFdvbykge1xuICAgICAgICAgICAgICAgICQoc2VsZWN0b3IpLnNlbGVjdFdvbyh7XG4gICAgICAgICAgICAgICAgICAgIGFsbG93Q2xlYXIgOiB0cnVlXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgQ29sb3JQaWNrZXIoc2VsZWN0b3IgPSAnaW5wdXQud3ZzLWNvbG9yLXBpY2tlcicpIHtcbiAgICAgICAgICAgIGlmICgkKCkud3BDb2xvclBpY2tlcikge1xuICAgICAgICAgICAgICAgICQoc2VsZWN0b3IpLndwQ29sb3JQaWNrZXIoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBGaWVsZERlcGVuZGVuY3koc2VsZWN0b3IgPSAnW2RhdGEtZGVwZW5kc10nKSB7XG4gICAgICAgICAgICBpZiAoJCgpLkZvcm1GaWVsZERlcGVuZGVuY3kpIHtcbiAgICAgICAgICAgICAgICAkKHNlbGVjdG9yKS5Gb3JtRmllbGREZXBlbmRlbmN5KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgc2F2aW5nRGlhbG9nKCR3cmFwcGVyLCAkZGlhbG9nLCB0YXhvbm9teSkge1xuXG4gICAgICAgICAgICBsZXQgZGF0YSA9IHt9O1xuICAgICAgICAgICAgbGV0IHRlcm0gPSAnJztcblxuICAgICAgICAgICAgLy8gQFRPRE86IFdlIHNob3VsZCB1c2UgZm9ybSBkYXRhLCBiZWNhdXNlIHdlIGhhdmUgdG8gcGljayBhcnJheSBiYXNlZCBkYXRhIGFsc28gOilcblxuICAgICAgICAgICAgJGRpYWxvZy5maW5kKGBpbnB1dCwgc2VsZWN0YCkuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgbGV0IGtleSAgID0gJCh0aGlzKS5hdHRyKCduYW1lJyk7XG4gICAgICAgICAgICAgICAgbGV0IHZhbHVlID0gJCh0aGlzKS52YWwoKTtcbiAgICAgICAgICAgICAgICBpZiAoa2V5KSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChrZXkgPT09ICd0YWdfbmFtZScpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRlcm0gPSB2YWx1ZVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVtrZXldID0gdmFsdWVcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnZhbCgnJylcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgaWYgKHRlcm0pIHtcbiAgICAgICAgICAgICAgICAkKCcucHJvZHVjdF9hdHRyaWJ1dGVzJykuYmxvY2soe1xuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlICAgIDogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgb3ZlcmxheUNTUyA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhY2tncm91bmQgOiAnI2ZmZicsXG4gICAgICAgICAgICAgICAgICAgICAgICBvcGFjaXR5ICAgIDogMC42XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGxldCBhamF4X2RhdGEgPSB7XG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbiAgIDogJ3dvb2NvbW1lcmNlX2FkZF9uZXdfYXR0cmlidXRlJyxcbiAgICAgICAgICAgICAgICAgICAgdGF4b25vbXkgOiB0YXhvbm9teSxcbiAgICAgICAgICAgICAgICAgICAgdGVybSAgICAgOiB0ZXJtLFxuICAgICAgICAgICAgICAgICAgICBzZWN1cml0eSA6IHdvb2NvbW1lcmNlX2FkbWluX21ldGFfYm94ZXMuYWRkX2F0dHJpYnV0ZV9ub25jZSxcbiAgICAgICAgICAgICAgICAgICAgLi4uZGF0YVxuICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICAkLnBvc3Qod29vY29tbWVyY2VfYWRtaW5fbWV0YV9ib3hlcy5hamF4X3VybCwgYWpheF9kYXRhLCBmdW5jdGlvbiAocmVzcG9uc2UpIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2UuZXJyb3IpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEVycm9yLlxuICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93LmFsZXJ0KHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlIGlmIChyZXNwb25zZS5zbHVnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBTdWNjZXNzLlxuICAgICAgICAgICAgICAgICAgICAgICAgJHdyYXBwZXIuZmluZCgnc2VsZWN0LmF0dHJpYnV0ZV92YWx1ZXMnKS5hcHBlbmQoJzxvcHRpb24gdmFsdWU9XCInICsgcmVzcG9uc2UudGVybV9pZCArICdcIiBzZWxlY3RlZD1cInNlbGVjdGVkXCI+JyArIHJlc3BvbnNlLm5hbWUgKyAnPC9vcHRpb24+Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkd3JhcHBlci5maW5kKCdzZWxlY3QuYXR0cmlidXRlX3ZhbHVlcycpLmNoYW5nZSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnLnByb2R1Y3RfYXR0cmlidXRlcycpLnVuYmxvY2soKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJy5wcm9kdWN0X2F0dHJpYnV0ZXMnKS51bmJsb2NrKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgQXR0cmlidXRlRGlhbG9nKCkge1xuXG4gICAgICAgICAgICBsZXQgc2VsZiA9IHRoaXM7XG4gICAgICAgICAgICAkKCcucHJvZHVjdF9hdHRyaWJ1dGVzJykub24oJ2NsaWNrJywgJ2J1dHRvbi53dnNfYWRkX25ld19hdHRyaWJ1dGUnLCBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICAgICBsZXQgJHdyYXBwZXIgID0gJCh0aGlzKS5jbG9zZXN0KCcud29vY29tbWVyY2VfYXR0cmlidXRlJyk7XG4gICAgICAgICAgICAgICAgbGV0IGF0dHJpYnV0ZSA9ICR3cmFwcGVyLmRhdGEoJ3RheG9ub215Jyk7XG4gICAgICAgICAgICAgICAgbGV0IHRpdGxlICAgICA9ICQodGhpcykuZGF0YSgnZGlhbG9nX3RpdGxlJyk7XG5cbiAgICAgICAgICAgICAgICAkKCcud3ZzLWF0dHJpYnV0ZS1kaWFsb2ctZm9yLScgKyBhdHRyaWJ1dGUpLmRpYWxvZyh7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlICAgICAgICAgOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgZGlhbG9nQ2xhc3MgICA6ICd3cC1kaWFsb2cgd3ZzLWF0dHJpYnV0ZS1kaWFsb2cnLFxuICAgICAgICAgICAgICAgICAgICBjbGFzc2VzICAgICAgIDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJ1aS1kaWFsb2dcIiA6IFwid3AtZGlhbG9nIHd2cy1hdHRyaWJ1dGUtZGlhbG9nXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgYXV0b09wZW4gICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBkcmFnZ2FibGUgICAgIDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgd2lkdGggICAgICAgICA6ICdhdXRvJyxcbiAgICAgICAgICAgICAgICAgICAgbW9kYWwgICAgICAgICA6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgIHJlc2l6YWJsZSAgICAgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgY2xvc2VPbkVzY2FwZSA6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgIHBvc2l0aW9uICAgICAgOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBteSA6IFwiY2VudGVyXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBhdCA6IFwiY2VudGVyXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBvZiA6IHdpbmRvd1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBvcGVuICAgICAgICAgIDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gY2xvc2UgZGlhbG9nIGJ5IGNsaWNraW5nIHRoZSBvdmVybGF5IGJlaGluZCBpdFxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnLnVpLXdpZGdldC1vdmVybGF5JykuYmluZCgnY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2F0dHJpYnV0ZS1kaWFsb2cnKS5kaWFsb2coJ2Nsb3NlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBjcmVhdGUgICAgICAgIDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gc3R5bGUgZml4IGZvciBXb3JkUHJlc3MgYWRtaW5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vICQoJy51aS1kaWFsb2ctdGl0bGViYXItY2xvc2UnKS5hZGRDbGFzcygndWktYnV0dG9uJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAuZGlhbG9nKFwib3B0aW9uXCIsIFwidGl0bGVcIiwgdGl0bGUpXG4gICAgICAgICAgICAgICAgICAgIC5kaWFsb2coXCJvcHRpb25cIiwgXCJidXR0b25zXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBbXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0ZXh0ICA6IFdWU1BsdWdpbk9iamVjdC5kaWFsb2dfc2F2ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xpY2sgOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWxmLnNhdmluZ0RpYWxvZygkd3JhcHBlciwgJCh0aGlzKSwgYXR0cmlidXRlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZGlhbG9nKFwiY2xvc2VcIikuZGlhbG9nKFwiZGVzdHJveVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0ZXh0ICA6IFdWU1BsdWdpbk9iamVjdC5kaWFsb2dfY2FuY2VsLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGljayA6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZGlhbG9nKFwiY2xvc2VcIikuZGlhbG9nKFwiZGVzdHJveVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICAgICAuZGlhbG9nKCdvcGVuJylcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIFBsdWdpbkhlbHBlcjtcbn0pKGpRdWVyeSk7XG5cbmV4cG9ydCB7IFBsdWdpbkhlbHBlciB9O1xuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBzcmMvanMvUGx1Z2luSGVscGVyLmpzIiwiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL3NyYy9zY3NzL2JhY2tlbmQuc2Nzc1xuLy8gbW9kdWxlIGlkID0gM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL3Njc3MvZnJvbnRlbmQuc2Nzc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL3Njc3MvdG9vbHRpcC5zY3NzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9zcmMvc2Nzcy90aGVtZS1vdmVycmlkZS5zY3NzXG4vLyBtb2R1bGUgaWQgPSA2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9zcmMvc2Nzcy9jdXN0b21pemUtaGVhZGluZy1jb250cm9sLnNjc3Ncbi8vIG1vZHVsZSBpZCA9IDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIl0sIm1hcHBpbmdzIjoiOzs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM3REE7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUNBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7QUMvQkE7QUFDQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQURBO0FBQUE7QUFBQTtBQUlBO0FBQ0E7QUFDQTtBQUNBO0FBUEE7QUFBQTtBQUFBO0FBU0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFMQTtBQUNBO0FBT0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBOURBO0FBQUE7QUFBQTtBQUNBO0FBaUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQTFFQTtBQUFBO0FBQUE7QUE0RUE7QUFDQTtBQUFBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQWxGQTtBQUFBO0FBQUE7QUFvRkE7QUFDQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBeEZBO0FBQUE7QUFBQTtBQTBGQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUE5RkE7QUFBQTtBQUFBO0FBQ0E7QUFpR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFGQTtBQUNBO0FBT0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUpBO0FBQ0E7QUFPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBeEpBO0FBQUE7QUFBQTtBQUNBO0FBMkpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQTFCQTtBQWdDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFRQTtBQUNBO0FBQ0E7QUFDQTtBQUpBO0FBU0E7QUFDQTtBQXJOQTtBQUNBO0FBREE7QUFBQTtBQUNBO0FBdU5BO0FBQ0E7QUFDQTs7Ozs7OztBQzVOQTs7Ozs7O0FDQUE7Ozs7OztBQ0FBOzs7Ozs7QUNBQTs7Ozs7O0FDQUE7OztBIiwic291cmNlUm9vdCI6IiJ9