<?php
namespace MyCompany\CustomerBlocklist\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Rules extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('email', ['label' => __('Email')]);
        $this->addColumn('telephone', ['label' => __('Telephone')]);
        $this->addColumn('firstname', ['label' => __('First Name')]);
        $this->addColumn('lastname', ['label' => __('Last Name')]);
        $this->addColumn('note', ['label' => __('Note')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }
}
