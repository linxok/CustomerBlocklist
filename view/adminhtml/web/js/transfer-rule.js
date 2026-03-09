define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirmation) {
    'use strict';

    window.customerBlocklistMoveRule = function(button, sourceList, targetList) {
        var $button = $(button);
        var $row = $button.closest('tr');
        
        if (!$row.length) {
            return;
        }

        var params = {
            source_list: sourceList,
            target_list: targetList,
            back_url: window.location.href
        };

        ['email', 'telephone', 'firstname', 'lastname', 'note'].forEach(function(field) {
            var $input = $row.find('input[name$="[' + field + ']"]');
            params[field] = $input.length ? $input.val() : '';
        });

        var message = targetList === 'whitelist' 
            ? $.mage.__('Move this rule to Whitelist?')
            : $.mage.__('Move this rule to Blacklist?');

        var url = $button.data('move-url');
        if (!url) {
            console.error('Move URL not found');
            return;
        }

        confirmation({
            title: $.mage.__('Confirm Transfer'),
            content: message,
            actions: {
                confirm: function() {
                    var queryString = $.param(params);
                    window.location.href = url + '?' + queryString;
                }
            }
        });
    };
});
