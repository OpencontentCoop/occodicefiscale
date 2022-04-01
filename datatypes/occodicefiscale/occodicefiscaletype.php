<?php

class OCCodiceFiscaleType extends eZStringType
{

    const DATA_TYPE_STRING = 'occodicefiscale';

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING,
            ezpI18n::tr('extension/occodicefiscale', 'Fiscal Code'),
            array(
                'serialize_supported' => true,
                'object_serialize_map' => array('data_text' => 'text')
            )
        );
    }

    /**
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $data = strtoupper($data);
            $contentObjectAttribute->setAttribute( 'data_text', $data );

            return true;
        }
        return false;
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }


    /**
     * This method is almost identical with the one from extended class,
     * It simply adds a call to a method dedicated to uniqueness validation
     *
     * @param string $data
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param eZContentClassAttribute $classAttribute
     * @return integer
     */
    function validateStringHTTPInput($data, $contentObjectAttribute, $classAttribute)
    {
        if (eZINI::instance('content.ini')->variable('CodiceFiscaleSettings', 'FormalValidation') == 'enabled') {
            $codiceFiscale = new CodiceFiscale();
            $codiceFiscale->SetCF($data);
            if (!$codiceFiscale->GetCodiceValido()) {
                $contentObjectAttribute->setValidationError($codiceFiscale->GetErrore());
                return eZInputValidator::STATE_INVALID;
            }
        }

        if (eZINI::instance('content.ini')->variable('CodiceFiscaleSettings', 'UniqueValidator') == 'enabled') {
            return self::validateUniqueStringHTTPInput($data, $contentObjectAttribute);
        }

        return eZInputValidator::STATE_ACCEPTED;
    }


    /**
     * This method checks if given string does exist in any content object
     * attributes with the same id, with the exception for those being versions
     * of the same content object. If given string exists anywhere, in published
     * or unpublished versions, drafts, trash, this string will be excluded.
     *
     * More information in the ini file uniquedatatypes.ini.append.php
     *
     * @param string $data
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return integer
     */
    private static function validateUniqueStringHTTPInput($data, $contentObjectAttribute)
    {
        $contentObjectID = $contentObjectAttribute->attribute('contentobject_id');
        $contentClassAttributeID = $contentObjectAttribute->attribute('contentclassattribute_id');
        $db = eZDB::instance();

        $query = "SELECT COUNT(*) AS datacounter
				FROM ezcontentobject co, ezcontentobject_attribute coa
				WHERE co.id = coa.contentobject_id
				AND co.current_version = coa.version
				AND co.status = " . eZContentObject::STATUS_PUBLISHED . "
				AND coa.contentobject_id <> " . $db->escapeString($contentObjectID) . "
				AND coa.contentclassattribute_id = " . $db->escapeString($contentClassAttributeID) . "
				AND coa.data_text = '" . $db->escapeString($data) . "'";


        $result = $db->arrayQuery($query);
        $resultCount = $result[0]['datacounter'];

        if ($resultCount) {
            $contentObjectAttribute->setValidationError(ezpI18n::tr('extension/occodicefiscale', 'The entered fiscal code is already used in the system'));
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param $string
     * @return bool
     */
    function fromString( $contentObjectAttribute, $string )
    {
        $isValid = true;
        if (eZINI::instance('content.ini')->variable('CodiceFiscaleSettings', 'FormalValidation') == 'enabled') {
            $codiceFiscale = new CodiceFiscale();
            $codiceFiscale->SetCF($string);
            $isValid = $codiceFiscale->GetCodiceValido();
        }

        if (eZINI::instance('content.ini')->variable('CodiceFiscaleSettings', 'UniqueValidator') == 'enabled') {
            $isValid = self::validateUniqueStringHTTPInput($string, $contentObjectAttribute) == eZInputValidator::STATE_ACCEPTED;
        }

        if ($isValid) {
            $contentObjectAttribute->setAttribute('data_text', $string);
            return true;
        }

        return false;
    }

    function isInformationCollector()
    {
        return false;
    }

    /**
     * @param string $data
     * @param int $contentClassAttributeID
     * @return bool|eZContentObject
     */
    public static function fetchObjectByCodiceFiscale($codiceFiscale, $contentClassAttributeID)
    {
        $db = eZDB::instance();

        $query = "SELECT co.id
				FROM ezcontentobject co, ezcontentobject_attribute coa
				WHERE co.id = coa.contentobject_id
				AND co.current_version = coa.version
				AND co.status = " . eZContentObject::STATUS_PUBLISHED . "				
				AND coa.contentclassattribute_id = " . $db->escapeString($contentClassAttributeID) . "
				AND coa.data_text = '" . $db->escapeString($codiceFiscale) . "'";

        $result = $db->arrayQuery($query);
        if (isset($result[0]['id'])){
            return eZContentObject::fetch((int)$result[0]['id']);
        }

        return false;
    }
}

eZDataType::register(OCCodiceFiscaleType::DATA_TYPE_STRING, "OCCodiceFiscaleType");

