;(function($, window, document, undefined) {
    "use strict";

    // Create the defaults once
    var pluginName = 'HueUserRegister',
        defaults = {
            progressInfoContainer: '#HueUserRegisterProgressInfo',
            registerButton: '.registerTrigger',
            progressBar: null,
            progressBarSeconds: null
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.progressInfoContainer = $(this.settings.progressInfoContainer);
        this.registerButton = $(element).find(this.settings.registerButton);
        this.formAction = element.action;

        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function() {
            this.registerEvents();
        },
        registerEvents: function() {
            var me = this;
            // some logic
            $(this.element).on('click', function(e) {
                e.preventDefault();

                me.registerButton.prop('disabled', true);
                me.call();
            });
        },
        call: function () {
            var me = this;

            if (!window.XMLHttpRequest){
                alert('Your browser does not support our Register process.');
                me.registerButton.prop('disabled', false);
                return;
            }

            try {
                var xhr = new XMLHttpRequest();
                xhr.prev = '';
                xhr.open('GET', me.formAction, true);
                xhr.onreadystatechange = function() {
                    var response = xhr.responseText.substring(xhr.prev.length);
                    if (response.length > 0 && me.isJsonString(response)) {
                        var result = JSON.parse(response);
                        xhr.prev = xhr.responseText;

                        if (xhr.readyState == 4) {
                            // done
                            if (result.type == 'SUCCESS') {
                                // Success
                                me.showSuccessText();
                                me.setProgressBarState(100);
                            }
                            me.registerButton.prop('disabled', false);
                        } else if (xhr.readyState > 2) {
                            // Progressbar state
                            if (me.settings.progressBar != null) {
                                me.setProgressSeconds(result.remainSeconds);
                                me.setProgressBarState(result.process);
                                // Process
                                me.showProgressText();
                            }

                            console.log(result);

                            if (result.process == 100) {
                                // Too late, process finished
                                me.showErrorAttemptText();
                            }
                        }
                    }
                };
                xhr.onerror = function() {
                    me.showErrorText();
                    console.error('Unexpected error appeared while user register.');
                };
                xhr.send();
            } catch (e) {
                me.showErrorText();
                console.error('Unable to send request.');
            }
        },
        /**
         * Checks if string is a json string
         * @param str
         * @returns {boolean}
         */
        isJsonString: function(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        },
        /**
         * Sets the progress bar state
         * @param progress
         */
        setProgressBarState: function (progress) {
            var progressBar = $(this.settings.progressBar);
            progressBar.css({
                'width': progress + '%'
            });
            progressBar.prop('aria-valuenow', progress);
        },
        /**
         * Shows the progress text
         */
        showProgressText: function () {
            var text = this.progressInfoContainer.data('progress-text');
            this.progressInfoContainer.addClass('progress');
            this.progressInfoContainer.html(text);
        },
        /**
         * Shows the success text
         */
        showSuccessText: function () {
            var text = this.progressInfoContainer.data('success-text');
            this.progressInfoContainer.addClass('success');
            this.progressInfoContainer.html(text);
        },
        /**
         * Shows the success text
         */
        showErrorText: function () {
            var text = this.progressInfoContainer.data('error-text');
            this.progressInfoContainer.addClass('error');
            this.progressInfoContainer.html(text);
        },
        /**
         * Shows the success text
         */
        showErrorAttemptText: function () {
            var text = this.progressInfoContainer.data('error-attempt-text');
            this.progressInfoContainer.addClass('error-attempt');
            this.progressInfoContainer.html(text);
        },
        /**
         * Sets the process seconds
         * @param $seconds
         */
        setProgressSeconds: function ($seconds) {
            $(this.settings.progressBarSeconds).html($seconds);
        },
        /**
         * Resets all text
         */
        resetText: function () {
            this.progressInfoContainer.html('');
        }
    });

    $.fn[pluginName] = function(options) {
        return this.each( function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" +
                    pluginName, new Plugin(this, options));
            }
        } );
    };

    // initiate plugin
    $('#HueUserRegisterForm').HueUserRegister({
        progressBar: '#HueUserRegisterProgress',
        progressBarSeconds: '#HueUserRegisterProgressSeconds'
    });
})(jQuery, window, document);
