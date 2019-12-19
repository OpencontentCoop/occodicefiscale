<?php

class OCDescriptionBooleanType extends eZBooleanType
{
    const DATA_TYPE_STRING = 'ocdescriptionboolean';

    function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING, ezpI18n::tr('kernel/classes/datatypes', "Checkbox with description", 'Datatype name'),
            array(
                'serialize_supported' => true,
                'object_serialize_map' => array('data_int' => 'value')
            )
        );
    }

    /**
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     * @return bool
     */
    function fetchClassAttributeHTTPInput($http, $base, $classAttribute)
    {
        if ($http->hasPostVariable($base . '_ezboolean_default_value_' . $classAttribute->attribute('id') . '_exists')) {
            if ($http->hasPostVariable($base . "_ezboolean_default_value_" . $classAttribute->attribute("id"))) {
                $data = $http->postVariable($base . "_ezboolean_default_value_" . $classAttribute->attribute("id"));
                if (isset($data)) {
                    $data = 1;
                }
                $classAttribute->setAttribute("data_int3", $data);
            } else {
                $classAttribute->setAttribute("data_int3", 0);
            }

            if ($http->hasPostVariable($base . "_ezboolean_text_" . $classAttribute->attribute("id"))) {
                $data = $http->postVariable($base . "_ezboolean_text_" . $classAttribute->attribute("id"));
                $classAttribute->setAttribute("data_text5", $data);
            } else {
                $classAttribute->setAttribute("data_text5", '');
            }
            if ($http->hasPostVariable($base . "_ezboolean_accept_text_" . $classAttribute->attribute("id"))) {
                $data = $http->postVariable($base . "_ezboolean_accept_text_" . $classAttribute->attribute("id"));
                $classAttribute->setAttribute("data_text4", $data);
            } else {
                $classAttribute->setAttribute("data_text4", '');
            }
        }
        
        return true;
    }

    /**
     * @param eZContentClassAttribute $classAttribute
     * @param DOMElement $attributeNode
     * @param DOMElement $attributeParametersNode
     */
    function serializeContentClassAttribute($classAttribute, $attributeNode, $attributeParametersNode)
    {
        $defaultValue = $classAttribute->attribute('data_int3');
        $dom = $attributeParametersNode->ownerDocument;
        $defaultValueNode = $dom->createElement('default-value');
        $defaultValueNode->setAttribute('is-set', $defaultValue ? 'true' : 'false');
        $attributeParametersNode->appendChild($defaultValueNode);
        $textValueNode = $dom->createElement('text-value');
        $textValueNode->textContent = $classAttribute->attribute('data_text5');
        $attributeParametersNode->appendChild($textValueNode);
        $cceptTextValueNode = $dom->createElement('accept-value');
        $cceptTextValueNode->textContent = $classAttribute->attribute('data_text4');
        $attributeParametersNode->appendChild($cceptTextValueNode);
    }

    /**
     * @param eZContentClassAttribute $classAttribute
     * @param DOMElement $attributeNode
     * @param DOMElement $attributeParametersNode
     */
    function unserializeContentClassAttribute($classAttribute, $attributeNode, $attributeParametersNode)
    {

        $defaultValue = strtolower($attributeParametersNode->getElementsByTagName('default-value')->item(0)->getAttribute('is-set')) == 'true';
        $classAttribute->setAttribute('data_int3', $defaultValue);
        $text = $attributeParametersNode->getElementsByTagName('text-value')->item(0)->textContent;
        $classAttribute->setAttribute('data_text5', $text);
        $text = $attributeParametersNode->getElementsByTagName('accept-value')->item(0)->textContent;
        $classAttribute->setAttribute('data_text5', $text);
    }

    function isInformationCollector()
    {
        return false;
    }

}


eZDataType::register(OCDescriptionBooleanType::DATA_TYPE_STRING, "OCDescriptionBooleanType");