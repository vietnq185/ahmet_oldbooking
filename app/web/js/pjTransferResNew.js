/*!
 * Transfer Reservation v1.0
 * http://www.phpjabbers.com/transfer-reservation/
 *
 * Copyright 2014, StivaSoft Ltd.
 *
 */
(function (window, undefined){
    "use strict";

    pjQ.$.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });

    var document = window.document,
        isPageLoaded = false,
        validate = (pjQ.$.fn.validate !== undefined),
        datepicker = (pjQ.$.fn.datepicker !== undefined);

    function log() {
        if (window.console && window.console.log) {
            for (var x in arguments) {
                if (arguments.hasOwnProperty(x)) {
                    window.console.log(arguments[x]);
                }
            }
        }
    }

    function assert() {
        if (window && window.console && window.console.assert) {
            window.console.assert.apply(window.console, arguments);
        }
    }

    function TransferResNew(opts) {
        if (!(this instanceof TransferResNew)) {
            return new TransferResNew(opts);
        }

        this.reset.call(this);
        this.init.call(this, opts);

        return this;
    }

    TransferResNew.inObject = function (val, obj) {
        var key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (obj[key] == val) {
                    return true;
                }
            }
        }
        return false;
    };

    TransferResNew.size = function(obj) {
        var key,
            size = 0;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                size += 1;
            }
        }
        return size;
    };

    TransferResNew.prototype = {
        reset: function () {
            this.$container = null;
            this.container = null;
            this.page = null;
            this.opts = {};

            return this;
        },
        pageLoaded: function () {
            // Created this method instead of using window.onload event because sometimes the event is triggered before DOM is loaded.
            // Then jQuery can't find the preloader and fails to hide it, which causes infinite "loading" screen even if the page is loaded successfully.

            // UNIFY HEIGHT
            var maxHeight = 0;

            pjQ.$('.heightfix').each(function(){
                if (pjQ.$(this).height() > maxHeight) { maxHeight = pjQ.$(this).height(); }
            });
            pjQ.$('.heightfix').height(maxHeight);

            // PRELOADER
            pjQ.$('.preloader').fadeOut();

            isPageLoaded = true;
        },
        disableButtons: function () {
            var $el;
            this.$container.find(".btn").each(function (i, el) {
                $el = pjQ.$(el).attr("disabled", "disabled");
                if ($el.hasClass("btn")) {
                    $el.addClass("trButtonDisable");
                }
            });
        },
        enableButtons: function () {
            this.$container.find(".btn").removeAttr("disabled").removeClass("trButtonDisable");
        },
        init: function (opts) {
            var self = this;
            this.opts = opts;
            this.container = document.getElementById("trContainer_" + this.opts.index);
            this.$container = pjQ.$(this.container);

            pjQ.$.validator.setDefaults({
                onkeyup: false,
                errorElement: 'em',
                errorClass: 'trError',
                errorPlacement: function (error, element) {
                    if(element.is('select'))
                    {
                        if(element.hasClass('select2'))
                        {
                            error.insertAfter(element.next('.select2-container'));
                        }
                        else
                        {
                            error.insertAfter(element.parent());
                        }
                    }
                    else if(element.hasClass('hasDatepicker'))
                    {
                        error.insertAfter(element.parent());
                    }
                    else if(element.is(':checkbox'))
                    {
                        error.appendTo(element.closest('.f-row'));
                    }
                    else
                    {
                        error.insertAfter(element);
                    }
                }
            });

            this.$container.on("click.tr", ".btn", function (e) {
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    if (e && e.preventDefault) {
                        e.preventDefault();
                    }
                }
            }).on("click.tr", ".trSelectorLocale", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var locale = pjQ.$(this).data("id");
                self.opts.locale = locale;
                pjQ.$(this).addClass("trLocaleFocus").parent().parent().find("a.trSelectorLocale").not(this).removeClass("trLocaleFocus");

                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale", "&session_id=", self.opts.session_id].join(""), {
                    "locale_id": locale
                }).done(function (data) {
                    window.location.reload();
                }).fail(function () {
                    log("Deferred is rejected");
                });
                return false;
            }).on("change.tr", "#trLocationId_"+ self.opts.index, function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.getLocations();
            }).on("click.tr", ".trChooseDateButton", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#trDate_' + self.opts.index).focus();
                return false;
            });

            pjQ.$(document).on("select2:open", function() {
                pjQ.$(".select2-search--dropdown .select2-search__field").attr("placeholder", self.opts.search_placeholder);
            });

            self.loadSearch.call(self);
        },
        loadSearch: function() {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSearchNew", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                self.$container.html(data);
                if(pjQ.$('#trReturnOn_' + index).is(':checked'))
                {
                    pjQ.$('.trReturnField').prop('disabled', false);
                }else{
                    pjQ.$('.trReturnField').prop('disabled', 'disabled');
                }

                self.bindSearch.call(self);
                if(parseInt(pjQ.$('#autoloadNextStep_' + index).val(), 10) == 1)
                {
                    pjQ.$('html, body').animate({
                        scrollTop: self.$container.offset().top
                    }, 0);

                    self.loadServices.call(self);
                }
                else
                {
                    /*
                     pjQ.$('html, body').animate({
                     scrollTop: self.$container.offset().top
                     }, 500);
                     */
                    self.pageLoaded.call(self);
                }
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindSearch: function(){
            var self = this;
            var $form = pjQ.$('#trSearchForm_' + self.opts.index);

            if($form.find("#trDate_" + self.opts.index).length > 0)
            {
                pjQ.$('#trDate_' + self.opts.index).datepicker({
                    showMillisec: false,
                    showMicrosec: false,
                    showTimezone: false,
                    showTime: false,
                    showHour: false,
                    showMinute: false,
                    numberOfMonths: 1,
                    minDate: new Date(),
                    firstDay: self.opts.startDay,
                    dateFormat: self.opts.jsDateFormat,
                    dayNames: self.opts.dayNames,
                    monthNames: self.opts.monthNamesFull,
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false }
                }).on("change", function (e) {
                    pjQ.$(this).valid();
                });
                pjQ.$('.ui-datepicker').addClass('notranslate');
            }

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();
            $form.find("select.select2").select2({
                width: "100%",
                templateResult: function (state) {
                    return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
                }
            });

            $form.validate({
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckNew", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 100:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.no_fleet).show().fadeOut(3000);
                                self.enableButtons.call(self);
                                break;
                            case 101:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.invalid_date).show().fadeOut(5000);
                                self.enableButtons.call(self);
                                break;
                            case 200:
                                // self.page = 1;
                                // self.loadServices.call(self);

                                window.location.href = self.opts.siteURL + '?' + $form.serialize();
                                break;
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });
        },
        getLocations: function(){
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index,
                    "location_id" : pjQ.$("#trLocationId_"+ index).val(),
                    "date" : pjQ.$("#trDate_"+ index).val()
                };
            self.disableButtons();
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocationsNew", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                pjQ.$('#dropoffBox_' + index).html(data);
                pjQ.$('#dropoffBox_' + index + ' select').select2({
                    width: "100%",
                    templateResult: function (state) {
                        return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
                    }
                });
                self.enableButtons.call(self);

                var $form = pjQ.$("#trSearchForm_"+ self.opts.index),
                    $location_id = pjQ.$("#trLocationId_"+ self.opts.index).val(),
                    $dropoff_id = pjQ.$("#trDropoffId_"+ self.opts.index).val(),
                    $date = pjQ.$("#trDate_"+ self.opts.index).val();
                if (parseInt($location_id) > 0 && parseInt($dropoff_id) > 0 && $date !== '') {
                    $form.trigger('submit');
                }
            }).fail(function () {
                self.enableButtons.call(self);
            });
        }
    };

    window.TransferResNew = TransferResNew;
})(window);