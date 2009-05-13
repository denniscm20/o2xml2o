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

 **************************************************************************/

/**
 * This class allows any child class to be exported into an XML structure or
 * allows it to import its attributes' values from a previously exported
 * XML file.
 * This class is the XML (un)serializer for your objects!!!
 * @author Dennis Cohn Muroy <dennis.cohn@pucp.edu.pe>
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.5
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 * @abstract
 */
abstract class XMLserializer
{
    /**
     * Converts an object into a XML Structure
     * @author Dennis Cohn Muroy
     * @access private
     * @final
     * @param Object $value Object to be converted to an XML structure
     */
    private function objectXML($value)
    {
        $funct = "writeXML"; 
        if (is_callable(array($value,$funct),true) !== FALSE) {
            $value->{$funct}(false);
        }
    }

    /**
     * Writes the value of a XML node.
     * @author Dennis Cohn Muroy
     * @access private
     * @param Object $value Value to be displayed in a XML node
     */
    private function valueXML($value)
    {
        print "{$value}\n";
    }

    /**
     * Converts a list of attributes with is assigned values into XML nodes
     * @author Dennis Cohn Muroy
     * @access private
     * @param mixed $elements List of attributes with its assigned values
     */
    private function arrayXML($elements)
    {
        foreach($elements as $key => $value) {
            $type = "";
            $class = "";
            if (is_object($value)) {
                $type = "Object";
                $class = get_class($value);
            } else if (is_array($value)) {
                $type = "Array";
                $class = "array";
            } else {
                $type = "Value";
                $class = gettype($value);
            }
            $keyTag = is_numeric($key)?"index_".$key:$key;
            print "<{$keyTag} type=\"{$type}\" class=\"{$class}\">\n";
            $type = $type."XML";
            $this->{$type}($value);
            print "</{$keyTag}>";
        }
    }

    /**
     * This is the method that must be called in order to convert a child object
     * into an XML structure.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     * @param bool $writeHeader Indicates if the xml header must be written.
     */
    public final function writeXML($writeHeader = true)
    {
        $attributes = get_object_vars($this);
        if ($writeHeader) {
            header ("content-type: text/xml");
            $objectName = get_class($this);
            print ("<{$objectName}>\n");
        }
        $this->arrayXML($attributes);
        if ($writeHeader) {
            print ("</{$objectName}>");
        }
    }

    /**
     * This function loads values of the node into an array.
     * @author Dennis Cohn Muroy
     * @access private
     * @param SimpleXMLElement $node Node of the XML Structure
     * @return array
     */
    private function getArrayStructure($node)
    {
        $innerArray = array();
        foreach ($node->children() as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];
            $name = $element->getName();
            $name = str_replace("index_","",$name);
            $class = (string)$attributes['class'];
            switch ($type) {
                case "Object":  $object = new $class();
                                $object->readStructure($node->children());
                                $innerArray["{$name}"] = $object;
                                break;
                case "Array":   $innerArray["{$name}"] = $this->getArrayStructure($element); break;
                case "Value":   $innerArray["{$name}"] = trim((string)$element); break;
            }
        }
        return $innerArray;
    }

    /**
     * This function loads values of the node into the object's attributes.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     * @param SimpleXMLElement $node Node of the XML Structure
     */
    public final function readStructure($node)
    {
        foreach ($node->children() as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];
            $name = $element->getName();
            switch ($type) {
                case "Object": $this->{$name}->readStructure($element); break;
                case "Array": $this->{$name} = $this->getArrayStructure($element); break;
                case "Value": $this->{$name} = trim((string)$element); break;
            }
        }
    }

    /**
     * This is the method that must be called in order to convert an XML
     * structure into an object.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     * @param string $file File or url that contains to the XML Structure.
     */
    public final function readXML($file)
    {
        $extension = "SimpleXML";
        if (!extension_loaded($extension)) {
            if (!dl($extension.".so")) {
                echo 'The SimpleXML could not not be loaded or is not installed';
                exit;
            }
        }
        $xml = new SimpleXMLElement($file, NULL, TRUE);
        $this->readStructure($xml);
    }
}

?>
