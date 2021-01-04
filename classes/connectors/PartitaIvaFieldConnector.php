<?php

use Opencontent\Ocopendata\Forms\Connectors\OpendataConnector\FieldConnector;

class PartitaIvaFieldConnector extends FieldConnector\StringField
{
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema['pattern'] = '^[0-9]{11}$';

        return $schema;
    }
}