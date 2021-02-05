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

    public function setPayload($postData)
    {
        if (!empty($postData)) {
            $this->validateCodiceFiscale($postData);
        }
        return $postData;
    }

    private function validateCodiceFiscale($data)
    {
        $dataType = $this->attribute->dataType();
        $fakeObjectAttribute = new \eZContentObjectAttribute([
            'contentobject_id' => (int)$this->getHelper()->getParameter('object'),
            'contentclassattribute_id' => $this->attribute->attribute('id')
        ]);
        if ($dataType->validateStringHTTPInput(
                $data,
                $fakeObjectAttribute,
                $this->attribute
            ) === \eZInputValidator::STATE_INVALID){
            throw new Exception($fakeObjectAttribute->validationError());
        }
    }
}