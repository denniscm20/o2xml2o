<?php

/************************************************************************
        XMLserializer - Copyright 2009 Dennis Cohn Muroy

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
 * This class allows any child class to be exported into an XML structure or
 * allows it to import its attributes' values from a previously exported
 * XML file.
 * This class is the XML (un)serializer for your objects!!!
 * @author Dennis Cohn Muroy <dennis.cohn@pucp.edu.pe>
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.7
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 */
class XMLserializer extends Serializer
{
    protected function writeNodeEnd($key, $type = "", $class = "") {
        print "</{$key}>";
    }

    protected function writeHeader() {
        header ("content-type: text/xml");
        $objectName = get_class($this);
        print ("<{$objectName}>\n");
    }
    
    protected function writeValue($value) {
        print "{$value}\n";
    }

    protected function writeFooter() {
        $objectName = get_class($this);
        print ("</{$objectName}>");
    }

    protected function writeNodeStart($key, $type = "", $class = "") {
        $keyTag = is_numeric($key)?"index_".$key:$key;
        print "<{$keyTag} type=\"{$type}\" class=\"{$class}\">\n";
    }

    protected function loadRequiredModules() {
        $extension = "SimpleXML";
        if (!extension_loaded($extension)) {
            if (!dl($extension.".so")) {
                echo 'The SimpleXML could not not be loaded or it is not installed';
                exit;
            }
        }
    }

    protected function retrieveStructure($file) {
        $structure = new SimpleXMLElement($file, NULL, TRUE);
        return $structure;
    }

    /**
     * This function loads values of the node into an array.
     * @author Dennis Cohn Muroy
     * @access protected
     * @param SimpleXMLElement $node Node of the XML Structure
     * @return array
     */
    protected function getArrayStructure($node)
    {
        $innerArray = array();
        foreach ($node->children() as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];
            $name = $element->getName();
            $name = str_replace("index_","",$name);
            $class = (string)$attributes['class'];
            switch ($type) {
                case self::SERIALIZER_OBJECT:
                    $object = new $class();
                    $object->readStructure($node->children());
                    $innerArray["{$name}"] = $object;
                    break;
                case self::SERIALIZER_ARRAY:
                    $innerArray["{$name}"] = $this->getArrayStructure($element); break;
                case self::SERIALIZER_VALUE:
                    $innerArray["{$name}"] = trim((string)$element); break;
            }
        }
        return $innerArray;
    }

    /**
     * This function loads values of the node into the object's attributes.
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     * @param SimpleXMLElement $node Node of the XML Structure
     */
    protected final function readStructure($node)
    {
        foreach ($node->children() as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];
            $name = $element->getName();
            switch ($type) {
                case self::SERIALIZER_OBJECT:
                    $this->{$name}->readStructure($element); break;
                case self::SERIALIZER_ARRAY:
                    $this->{$name} = $this->getArrayStructure($element); break;
                case self::SERIALIZER_VALUE:
                    $this->{$name} = trim((string)$element); break;
            }
        }
    }
}

?>
