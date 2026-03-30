<?php

namespace MyCompany\CustomerBlocklist\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class ActiveCheckbox extends AbstractBlock
{
    protected function _toHtml()
    {
        $inputName = (string)$this->getInputName();
        $inputId = (string)$this->getInputId();

        return '<input type="hidden" class="customerblocklist-rule-active" '
            . 'name="' . $this->escapeHtmlAttr($inputName) . '" '
            . 'id="' . $this->escapeHtmlAttr($inputId) . '" '
            . 'value="<%- typeof active !== \'undefined\' ? active : \'1\' %>" />'
            . '<input type="checkbox" class="customerblocklist-rule-active-toggle" value="1" '
            . '<% if (typeof active === \'undefined\' || active == \'1\') { %>checked="checked"<% } %> '
            . 'data-role="customerblocklist-rule-active-toggle" '
            . 'onclick="var hidden = this.parentNode.querySelector(\'.customerblocklist-rule-active\'); if (hidden) { hidden.value = this.checked ? \'1\' : \'0\'; }" />';
    }
}
