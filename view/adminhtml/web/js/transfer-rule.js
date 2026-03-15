define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirmation) {
    'use strict';

    function updateButtonState($button, isMarked) {
        var label = isMarked ? $button.attr('data-marked-label') : $button.attr('data-default-label');

        $button.toggleClass('customerblocklist-transfer-pending', isMarked);
        $button.find('span').text(label || '');
    }

    function moveRule(button) {
        var $button = $(button);
        var targetList = $button.attr('data-target-list');
        var $hidden = $button.siblings('.customerblocklist-transfer-action').first();

        if (!$hidden.length || !targetList) {
            return;
        }

        var message = targetList === 'whitelist' 
            ? $.mage.__('Mark this rule to move to Whitelist on Save Config?')
            : $.mage.__('Mark this rule to move to Blacklist on Save Config?');

        confirmation({
            title: $.mage.__('Confirm Transfer'),
            content: message,
            actions: {
                confirm: function() {
                    var newValue = $hidden.val() === targetList ? '' : targetList;
                    $hidden.val(newValue).trigger('change');
                    updateButtonState($button, !!newValue);
                }
            }
        });
    }

    function syncInitialState(context) {
        $(context || document).find('.customerblocklist-transfer').each(function () {
            var $button = $(this);
            var $hidden = $button.siblings('.customerblocklist-transfer-action').first();
            updateButtonState($button, !!$hidden.val());
        });
    }

    $(document).off('click.customerblocklistTransfer').on('click.customerblocklistTransfer', '.customerblocklist-transfer', function (event) {
        event.preventDefault();
        moveRule(this);
    });

    syncInitialState(document);

    window.customerBlocklistMoveRule = moveRule;
});
