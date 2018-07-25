(function($){
    "use strict";
    $.fn.settingsListen = function () {
        var $this = this;

        $this.find('#hmw_checksite').on('click', function () {
            $this.find('.wp_results').addClass('wp_loading');
            $this.find('.wp_results').html('');
            $.post(
                hmwQuery.ajaxurl,
                {
                    action: 'hmw_checkwebsite',
                    hmw_nonce: hmwQuery.nonce
                }
            ).done(function (response) {
                $this.find('.wp_results').removeClass('wp_loading');
                if(typeof response.html !== 'undefined') {
                    $this.find('.wp_results').html(response.html);
                }else{
                    $this.find('.wp_results').html('Could not check the website. Try: <a href="https://wpplugins.tips/wordpress-vulnerability-detector/" target="_blank">WordPress Vulnerability Detector</a>');
                }
            }).error(function () {
                $this.find('.wp_results').removeClass('wp_loading');
                $this.find('.wp_results').html('Could not check the website. Try: <a href="https://wpplugins.tips/wordpress-vulnerability-detector/" target="_blank">WordPress Vulnerability Detector</a>');
            });
            return false;
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function (html) {
            new Switchery(html, {color: 'green'});
        });

        $this.find('select[name=hmw_mode]').on('change', function () {
            $this.find('.tab-panel').hide();
            $this.find('.hmw_' + $(this).val()).show();
        });


        $this.find('#hmw_support button').on('click', function () {
            var form = $this.find('#hmw_support');
            if ( form.find("input#hmw_email").val() == ''){
                form.find("input#hmw_email").fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                return;
            }
            if ( form.find("textarea#hmw_question").val() == ''){
                form.find("textarea#hmw_question").fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                return;
            }
            $.post(
                form.attr('action'),
                {
                    action: 'hmw_support',
                    email: form.find("input#hmw_email").val(),
                    message: form.find("textarea#hmw_question").val(),
                    hmw_nonce: form.attr('nonce')
                }
            ).done(function (response) {
                if (typeof response.success !== 'undefined') {
                    form.find("#hmw_success").show();
                    form.find(".form-group").hide();

                } else {
                    form.find("#hmw_error").show();
                    form.find(".form-group").hide();
                }
            }).fail(function (response) {
                form.find("#hmw_error").show();
                form.find(".form-group").hide();
            }, 'json');
        });
    };

    $(document).ready(function () {
        $('#hmw_settings').settingsListen();
    });
})(jQuery);



