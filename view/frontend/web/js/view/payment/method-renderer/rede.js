/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Rede_Adquirencia/js/model/credit-card-validation/credit-card-data',
        'Rede_Adquirencia/js/model/credit-card-validation/credit-card-number-validator',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'jquery',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success'
    ],
    function (_, Component, creditCardData, cardNumberValidator, quote, $t, $, additionalValidators, redirectOnSuccessAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Rede_Adquirencia/payment/form',
                code: 'rede',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardOwner: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                creditCard: true,
                creditCard3Ds: false,
                debitCard: false,
                selectedCardType: null,
                installment: '1'
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardOwner',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'creditCard',
                        'creditCard3Ds',
                        'debitCard',
                        'selectedCardType',
                        'installment'
                    ]);
                return this;
            },

            /**
             * Init component
             */
            initialize: function () {
                var self = this;

                this._super();

                //Set Card number to Card data object
                this.creditCardNumber.subscribe(function (value) {
                    var result;

                    self.selectedCardType(null);

                    if (value === '' || value === null) {
                        return false;
                    }

                    result = cardNumberValidator(value);

                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }

                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });

                //Set creditCardOwner to Card data object
                this.creditCardOwner.subscribe(function (value) {
                    let owner = value.replace(/[^\w]/gi, '');

                    if (owner != value) {
                        return false;
                    }

                    creditCardData.creditCardOwner = owner;
                });

                //Set expiration year to Card data object
                this.creditCardExpYear.subscribe(function (value) {
                    creditCardData.expirationYear = value;
                });

                //Set expiration month to Card data object
                this.creditCardExpMonth.subscribe(function (value) {
                    creditCardData.expirationMonth = value;
                });

                //Set cvv code to Card data object
                this.creditCardVerificationNumber.subscribe(function (value) {
                    let cvv = value.replace(/[^\d]/g, '');

                    if (cvv != value) {
                        return false;
                    }

                    creditCardData.cvvCode = value;
                });

                //Set credit option
                this.creditCard.subscribe(function (value) {
                    let img = window.checkoutConfig.payment[this.getCode()].rede;

                    $('#issuers').attr('src', img);

                    creditCardData.creditCard = value;

                    if (value && self.hasInstallments()) {
                        $('#installments').show();
                    }
                });

                //Set 3ds option
                this.creditCard3Ds.subscribe(function (value) {
                    let img = window.checkoutConfig.payment[this.getCode()].rede;

                    if (value) {
                        img = window.checkoutConfig.payment[this.getCode()].rede_off;

                        if (self.hasInstallments()) {
                            $('#installments').show();
                        }
                    }

                    $('#issuers').attr('src', img);

                    creditCardData.creditCard3Ds = value;
                });

                //Set debit card option
                this.debitCard.subscribe(function (value) {
                    if (self.hasInstallments()) {
                        $('#rede_installments_div').toggle();
                    }

                    let img = window.checkoutConfig.payment[this.getCode()].rede;

                    if (value) {
                        img = window.checkoutConfig.payment[this.getCode()].rede_off;
                    }

                    $('#issuers').attr('src', img);
                    $('#installments').hide();

                    creditCardData.debitCard = value;
                });


                $([
                    window.checkoutConfig.payment[this.getCode()].rede,
                    window.checkoutConfig.payment[this.getCode()].rede_off
                ]).each(function () {
                    $('<img/>')[0].src = this;
                });
            },

            getDefaultIssuers: function() {
                return window.checkoutConfig.payment[this.getCode()].rede;
            },

            getCreditCard: function () {
                return window.checkoutConfig.payment[this.getCode()].creditCard;
            },

            getCreditCard3Ds: function () {
                return window.checkoutConfig.payment[this.getCode()].creditCard3Ds;
            },

            getDebitCard: function () {
                return window.checkoutConfig.payment[this.getCode()].debitCard;
            },

            getInstallments: function () {
                var installments = 0;
                var grandTotal = quote.totals().grand_total;
                var number_installments = window.checkoutConfig.payment[this.getCode()].number_installments;
                var min_total_installments = window.checkoutConfig.payment[this.getCode()].min_total_installments;

                if (Math.round(grandTotal / min_total_installments) >= number_installments) {
                    installments = number_installments
                } else {
                    installments = Math.round(grandTotal / min_total_installments)
                }

                function roundNumber(num, scale) {
                    if (!("" + num).includes("e")) {
                        return +(Math.round(num + "e+" + scale) + "e-" + scale);
                    } else {
                        var arr = ("" + num).split("e");
                        var sig = "";
                        if (+arr[1] + scale > 0) {
                            sig = "+";
                        }
                        return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
                    }
                }

                function numberWithCommas(x) {
                    return x.toFixed(2);
                }

                var array_installments = [];
                var installment_total = null;
                for (var i = 0; i < installments; i++) {
                    installment_total = roundNumber(grandTotal / (i + 1), 2);
                    array_installments.push({
                        value: i + 1,
                        key: i + 1 + 'x de R$ ' + numberWithCommas(installment_total) + ' sem juros'
                    })
                }
                return array_installments;
            },

            hasInstallments: function () {
                return this.debitCard && window.checkoutConfig.payment[this.getCode()].number_installments > 0;
            },

            is3DsEnabled: function () {
                let amount = quote.totals().grand_total;
                let enabled = window.checkoutConfig.payment[this.getCode()]["3ds_enabled"];
                let threshold = window.checkoutConfig.payment[this.getCode()]["3ds_threshold"];

                return enabled && amount >= threshold;
            },

            isDebitEnabled: function () {
                return window.checkoutConfig.payment[this.getCode()].debit_enabled;
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear(),
                        'cc_ss_issue': this.creditCardSsIssue(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'cc_owner': this.creditCardOwner(),
                        'number_of_installments': this.installment(),
                        'creditCard': this.creditCard(),
                        'creditCard3Ds': this.creditCard3Ds(),
                        'debitCard': this.debitCard()
                    }
                };
            },

            /**
             * Get list of available Card types
             * @returns {Object}
             */
            getCcAvailableTypes: function () {
                return window.checkoutConfig.payment.ccform.availableTypes[this.getCode()];
            },

            /**
             * Get payment icons
             * @param {String} type
             * @returns {Boolean}
             */
            getIcons: function (type) {
                return window.checkoutConfig.payment.ccform.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.ccform.icons[type]
                    : false;
            },

            /**
             * Get list of months
             * @returns {Object}
             */
            getCcMonths: function () {
                return window.checkoutConfig.payment.ccform.months[this.getCode()];
            },

            /**
             * Get list of years
             * @returns {Object}
             */
            getCcYears: function () {
                return window.checkoutConfig.payment.ccform.years[this.getCode()];
            },

            /**
             * Check if current payment has verification
             * @returns {Boolean}
             */
            hasVerification: function () {
                return window.checkoutConfig.payment.ccform.hasVerification[this.getCode()];
            },

            /**
             * @deprecated
             * @returns {Boolean}
             */
            hasSsCardType: function () {
                return window.checkoutConfig.payment.ccform.hasSsCardType[this.getCode()];
            },

            /**
             * Get image url for CVV
             * @returns {String}
             */
            getCvvImageUrl: function () {
                return window.checkoutConfig.payment.ccform.cvvImageUrl[this.getCode()];
            },

            /**
             * Get image for CVV
             * @returns {String}
             */
            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="' + $t('Card Verification Number Visual Reference') +
                    '" title="' + $t('Card Verification Number Visual Reference') +
                    '" />';
            },

            /**
             * @deprecated
             * @returns {Object}
             */
            getSsStartYears: function () {
                return window.checkoutConfig.payment.ccform.ssStartYears[this.getCode()];
            },

            /**
             * Get list of available Card types values
             * @returns {Object}
             */
            getCcAvailableTypesValues: function () {
                return _.map(this.getCcAvailableTypes(), function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    };
                });
            },

            /**
             * Get list of available month values
             * @returns {Object}
             */
            getCcMonthsValues: function () {
                return _.map(this.getCcMonths(), function (value, key) {
                    return {
                        'value': key,
                        'month': value
                    };
                });
            },

            /**
             * Get list of available year values
             * @returns {Object}
             */
            getCcYearsValues: function () {
                return _.map(this.getCcYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
            },

            /**
             * @deprecated
             * @returns {Object}
             */
            getSsStartYearsValues: function () {
                return _.map(this.getSsStartYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
            },

            /**
             * Is legend available to display
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return false;
            },

            /**
             * Get available Card type by code
             * @param {String} code
             * @returns {String}
             */
            getCcTypeTitleByCode: function (code) {
                var title = '',
                    keyValue = 'value',
                    keyType = 'type';

                _.each(this.getCcAvailableTypesValues(), function (value) {
                    if (value[keyValue] === code) {
                        title = value[keyType];
                    }
                });

                return title;
            },

            /**
             * Prepare Card number to output
             * @param {String} number
             * @returns {String}
             */
            formatDisplayCcNumber: function (number) {
                return 'xxxx-' + number.substr(-4);
            },

            /**
             * Get Card details
             * @returns {Array}
             */
            getInfo: function () {
                return [
                    {
                        'name': 'Card Type', value: this.getCcTypeTitleByCode(this.creditCardType())
                    },
                    {
                        'name': 'Card Number', value: this.formatDisplayCcNumber(this.creditCardNumber())
                    }
                ];
            },

            _validateHandler: function () {
                return $('#rede-form').validation && $('#rede-form').validation('isValid');
            },

            /**
             * @returns {Object}
             */
            context: function () {
                return this;
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return 'rede';
            },

            /**
             * @returns {Boolean}
             */
            isActive: function () {
                return true;
            },

            cleanValues: function () {
                this.creditCardVerificationNumber('');
                this.creditCardSsStartMonth('');
                this.creditCardSsStartYear('');
                this.creditCardSsIssue('');
                this.creditCardType('');
                this.creditCardExpYear('');
                this.creditCardExpMonth('');
                this.creditCardNumber('');
                this.creditCardOwner('');
                this.installment(1);
                this.creditCard(true);
                this.creditCard3Ds(false);
                this.debitCard(false);
            },

            /**
             * @override
             */
            /**
             * Place order.
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                                self.cleanValues();
                            }
                        ).done(
                        function () {
                            self.afterPlaceOrder();

                            if (self.redirectAfterPlaceOrder) {
                                redirectOnSuccessAction.execute();
                            }
                        }
                    );

                    return true;
                }

                return false;
            }
        });
    }
);
