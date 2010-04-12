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
 * @copyright Copyright (c) 2010, Dennis Cohn Muroy
 * @version 0.9
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 * @abstract
 */
abstract class XMLserializer
{
    const OPEN_TAG = "<%s type=\"%s\" class=\"%s\">\n";
    const CLOSE_TAG = "</%s>\n";

    /**
     * Exports a list of elements into XML
     * @author Dennis Cohn Muroy
     * @access private
     * @param array $elements List of elements to be exported into XML
     */
    private function printElements($elements)
    {
        foreach ($elements as $key => $value)
        {
            $keyTag = is_numeric($key)?"index_".$key:$key;
            if (is_object($value)) {
                $this->printObject($value, $keyTag);
            } else if (is_array($value)) {
                $this->printArray($value, $keyTag);
            } else {
                $this->printValue($value, $keyTag);
            }
        }
    }

    /**
     * Exports an object into a XML Structure
     * @author Dennis Cohn Muroy
     * @access private
     * @param Object $object Object to be exported to XML
     * @param string $attributeName Name of the attribute where the $object is
     * assigned.
     */
    private function printObject($object, $attributeName)
    {
        $class = get_class($object);
        $attributes = get_object_vars($object);
        echo sprintf(self::OPEN_TAG, $attributeName, 'Object', $class);
        $this->printElements($attributes);
        echo sprintf(self::CLOSE_TAG, $attributeName);
    }

    /**
     * Writes the value of a XML node.
     * @author Dennis Cohn Muroy
     * @access private
     * @param string|integer|boolean $value Value to be exported to a XML node
     * @param string $attributeName Name of the attribute where the $value is
     * assigned.
     */
    private function printValue($value, $attributeName)
    {
        $class = gettype($value);
        echo sprintf(self::OPEN_TAG, $attributeName, 'Value', $class);
        echo $value;
        echo sprintf(self::CLOSE_TAG, $attributeName);
    }

    /**
     * Exports a list of elements to XML
     * @author Dennis Cohn Muroy
     * @access private
     * @param array $array List of elements to be exported to XML.  These
     * elements can be Objects, arrays or basic datatypes.
     */
    private function printArray($array, $attributeName)
    {
        echo sprintf(self::OPEN_TAG, $attributeName, 'Array', 'array');
        $this->printElements($array);
        echo sprintf(self::CLOSE_TAG, $attributeName);
    }

    /**
     * Returns the value that will be assigned to the object attribute
     * @author Dennis Cohn Muroy
     * @access private
     * @param mixed $element Element that will be imported from XML into an
     * Object, Array or Basic Datatype variable.
     * @param string $type Indicates if the $element is an Object, an Array or
     * a basic datatype.
     * @param string $class Class of the object that was previously exported
     * into XML.
     * @return mixed Value to be assigned to an object attribute.
     */
    private function readElement($element, $type, $class)
    {
        switch ($type) {
            case "Object":
                $object = new $class();
                if (method_exists($object, "readStructure")) {
                    $object->readStructure($element);
                }
                return $object;
            case "Array":
                return $this->readArrayStructure($element);
            case "Value":
                return (string)trim($element);
        }
    }

    /**
     * This function loads values of the node into an array.
     * @author Dennis Cohn Muroy
     * @access private
     * @param SimpleXMLElement $node Node of the XML Structure
     * @return array
     */
    private function readArrayStructure($node)
    {
        $innerArray = array();
        foreach ($node->children() as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];
            $name = $element->getName();
            /**
             * @todo It must replace only the first occurrence
             */
            $name = str_replace("index_","",$name);
            $class = (string)$attributes['class'];
            $innerArray["{$name}"] = $this->readElement($element, $type, $class);
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
            $class = (string)$attributes['class'];
            $this->{$name} = $this->readElement($element, $type, $class);
        }
    }

    /**
     * This is the method that must be called in order to convert an XML
     * structure into an object that extends the XMLbridge class.
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

    /**
     * This is the method that must be called in order to export the child object
     * into an XML structure.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     */
    public final function writeXML()
    {
        header ("content-type: text/xml");
        $this->printObject($this, get_class($this));
    }
}

?>
