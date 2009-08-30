<?php

/************************************************************************
        JSONserializer - Copyright 2009 Dennis Cohn Muroy

This file is part of o2xml2o.

o2xml2o is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

o2xml2o is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with o2xml2o.  If not, see <http://www.gnu.org/licenses/>.

***************************************************************************/

require_once 'Base/Serializer.php';

/**
 * This class allows any child class to be exported into an JSON structure or
 * allows it to import its attributes' values from a previously exported
 * JSON file.
 * This class is the XML (un)serializer for your objects!!!
 * @author Dennis Cohn Muroy <dennis.cohn@pucp.edu.pe>
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.1
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 */
class JSONSerializer extends Serializer
{
    
    protected function writeHeader()
    {
        $objectName = get_class($this);
        print ("{\"{$objectName}\": { ");
    }
    
    protected function loadRequiredModules()
    {
    }
    
    protected function writeNodeStart($key, $type = "", $class = "")
    {
        $separator = "";
        switch ($type) {
            case self::SERIALIZER_OBJECT:
                $separator = "{";
                break;
            case self::SERIALIZER_ARRAY:
                $separator = "[";
                break;
            case self::SERIALIZER_VALUE:
                $separator = "";
                break;
        }
        print "\"{$key}\": {$separator}";
    }

    protected function writeNodeEnd($key, $type = "", $class = "")
    {
        $separator = "";
        switch ($type) {
            case self::SERIALIZER_OBJECT:
                $separator = "}";
                break;
            case self::SERIALIZER_ARRAY:
                $separator = "]";
                break;
            case self::SERIALIZER_VALUE:
                $separator = "";
                break;
        }
        print "{$separator},";
    }
    
    protected function readStructure($node) {

    }

    protected function writeValue($value) {
        print "\"{$value}\"";
    }
    
    protected function retrieveStructure($file) {
    }

    protected function writeFooter() {
        print ("} }");
    }
    
    protected function getArrayStructure($node) {
    }
}
?>
