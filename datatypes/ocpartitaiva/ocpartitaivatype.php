<?php

class OCPartitaIvaType extends eZStringType
{

    const DATA_TYPE_STRING = 'ocpartitaiva';

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING,
            ezpI18n::tr('extension/ocpartitaiva', 'VAT number'),
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
    function fetchObjectAttributeHTTPInput($http, $base, $contentObjectAttribute)
    {
        if ($http->hasPostVariable($base . '_ezstring_data_text_' . $contentObjectAttribute->attribute('id'))) {
            $data = $http->postVariable($base . '_ezstring_data_text_' . $contentObjectAttribute->attribute('id'));
            $data = strtoupper($data);
            $contentObjectAttribute->setAttribute('data_text', $data);

            return true;
        }
        return false;
    }

    function validateClassAttributeHTTPInput($http, $base, $classAttribute)
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
        $controllaPartitaIVA = $this->controllaPartitaIVA($data);
        if ($controllaPartitaIVA !== true) {
            $contentObjectAttribute->setValidationError($controllaPartitaIVA);
            return eZInputValidator::STATE_INVALID;
        }

        if (eZINI::instance('content.ini')->variable('PartitaIvaSettings', 'UniqueValidator') == 'enabled') {
            return self::validateUniqueStringHTTPInput($data, $contentObjectAttribute);
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    // Fonte: http://www.icosaedro.it/cf-pi/
    function controllaPartitaIVA($pi)
    {
        if ($pi === '') return 'La Partita IVA deve essere composta da 11 caratteri numerici';
        elseif (strlen($pi) != 11) return 'La Partita IVA deve essere composta da 11 caratteri numerici';
        elseif (preg_match("/^[0-9]+\$/D", $pi) != 1) return 'La Partita IVA deve contenere solo numeri';
        else {
            $s = $c = 0;
            for ($i = 0; $i <= 9; $i += 2) {
                $s += ord($pi[$i]) - ord('0');
            }
            for ($i = 1; $i <= 9; $i += 2) {
                $c = 2 * (ord($pi[$i]) - ord('0'));
                if ($c > 9) $c = $c - 9;
                $s += $c;
            }
            $controllo = (10 - $s % 10) % 10;
            if ($controllo != (ord($pi[10]) - ord('0'))) {
                return 'La Partita IVA non sembra valida: il codice di controllo non corrisponde';
            } else {
                return true;
            }
        }
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
				AND coa.contentobject_id <> " . $db->escapeString($contentObjectID) . "
				AND coa.contentclassattribute_id = " . $db->escapeString($contentClassAttributeID) . "
				AND coa.data_text = '" . $db->escapeString($data) . "'";


        $result = $db->arrayQuery($query);
        $resultCount = $result[0]['datacounter'];

        if ($resultCount) {
            $contentObjectAttribute->setValidationError(ezpI18n::tr('extension/ocpartitaiva', 'The VAT number entered is already used in the system'));
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
        if ($this->controllaPartitaIVA($string) === true) {

            $isValid = true;
            if (eZINI::instance('content.ini')->variable('PartitaIvaSettings', 'UniqueValidator') == 'enabled') {
                $isValid = self::validateUniqueStringHTTPInput($string, $contentObjectAttribute) == eZInputValidator::STATE_ACCEPTED;
            }

            if ($isValid) {
                $contentObjectAttribute->setAttribute('data_text', $string);

                return true;
            }
        }

        return false;
    }

    function isInformationCollector()
    {
        return false;
    }
}

eZDataType::register(OCPartitaIvaType::DATA_TYPE_STRING, "OCPartitaIvaType");

