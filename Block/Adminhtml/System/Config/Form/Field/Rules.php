<?php

namespace MyCompany\CustomerBlocklist\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Rules extends AbstractFieldArray
{

    protected function _prepareToRender()
    {
        $this->addColumn('email', ['label' => __('Email'), 'style' => 'width: 180px;', 'size' => '25']);
        $this->addColumn('telephone', ['label' => __('Tel'), 'style' => 'width: 120px;', 'size' => '15']);
        $this->addColumn('firstname', ['label' => __('First'), 'style' => 'width: 100px;', 'size' => '12']);
        $this->addColumn('lastname', ['label' => __('Last'), 'style' => 'width: 100px;', 'size' => '12']);
        $this->addColumn('note', ['label' => __('Note'), 'style' => 'width: 200px;', 'size' => '30']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    public function render(AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);
        $htmlId = $element->getHtmlId();

        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        if ($element->getIsDisableInheritance()) {
            $element->setReadonly(true);
        }

        $html = '<td class="label"></td>';
        $html .= '<td class="value">';
        $html .= '<div class="admin__field-label" style="float:none; width:auto; text-align:left; margin-bottom:8px; padding:0;">';
        $html .= '<label for="' . $element->getHtmlId() . '"><span' . $this->_renderScopeLabel($element) . '>' . $element->getLabel() . '</span></label>';
        $html .= '</div>';
        $html .= $this->_getElementHtml($element);


        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }

        $html .= '</td>';

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $defaults = [
            'email' => '',
            'telephone' => '',
            'firstname' => '',
            'lastname' => '',
            'note' => '',
        ];

        $columnValues = (array)$row->getData('column_values');
        $rowId = (string)$row->getData('_id');

        foreach ($defaults as $field => $defaultValue) {
            if ($row->getData($field) === null) {
                $row->setData($field, $defaultValue);
            }

            $columnInputId = $this->_getCellInputElementId($rowId, $field);
            if (!array_key_exists($columnInputId, $columnValues)) {
                $columnValues[$columnInputId] = (string)$row->getData($field);
            }
        }

        $row->setData('column_values', $columnValues);
    }

}
