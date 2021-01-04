<?php

use Opencontent\Ocopendata\Forms\Connectors\OpendataConnector\FieldConnector;

class CodiceFiscaleFieldConnector extends FieldConnector\StringField
{
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema['pattern'] = '^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$';

        return $schema;
    }
}