<?php

use Opencontent\Ocopendata\Forms\Connectors\OpendataConnector\FieldConnector;

class DescriptionBooleanFieldConnector extends FieldConnector\BooleanField
{
    public function getSchema()
    {
        return array(
            "type" => "boolean",
            "title" => $this->attribute->attribute('name'),
            'required' => (bool)$this->attribute->attribute('is_required')
        );
    }

    public function getOptions()
    {
        return array(
            "helper" => $this->attribute->attribute('data_text5'),
            'type' => 'checkbox',
            'rightLabel' => $this->attribute->attribute('data_text4')
        );
    }
}