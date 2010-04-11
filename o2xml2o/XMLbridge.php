<?php

/************************************************************************
        XMLbridge - Copyright 2009 Dennis Cohn Muroy

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
 * This class is the XML bridge for your objects!!!
 * @author Dennis Cohn Muroy
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.8
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 * @abstract
 */
abstract class XMLbridge
{
    const OPEN_TAG = "<element name=\"%s\" type=\"%s\" class=\"%s\">\n";
    const CLOSE_TAG = "</element>\n";

    /**
     * Converts an object into a XML Structure
     * @author Dennis Cohn Muroy
     * @access private
     * @param Object $object
     * @param string $attributeName
     */
    private function printObject($object, $attributeName)
    {
        $class = get_class($object);
        $attributes = get_object_vars($object);
        echo sprintf(self::OPEN_TAG, $attributeName, 'Object', $class);
        $this->printElements($attributes);
        echo self::CLOSE_TAG;
    }

    private function printElements($elements)
    {
        foreach ($elements as $key => $value)
        {
            if (is_object($value)) {
                $this->printObject($value, $key);
            } else if (is_array($value)) {
                $this->printArray($value, $key);
            } else {
                $this->printValue($value, $key);
            }
        }
    }

    /**
     * Writes the value of a XML node.
     * @author Dennis Cohn Muroy
     * @access private
     * @param Object $value Value to be displayed in a XML node
     */
    private function printValue($value, $attributeName)
    {
        $class = gettype($value);
        echo sprintf(self::OPEN_TAG, $attributeName, 'Value', $class);
        echo $value;
        echo self::CLOSE_TAG;
    }

    /**
     * Converts a list of attributes with its assigned values into XML nodes
     * @author Dennis Cohn Muroy
     * @access private
     * @param mixed $elements List of attributes with its assigned values
     */
    private function printArray($array, $attributeName)
    {
        echo sprintf(self::OPEN_TAG, $attributeName, 'Array', 'array');
        $this->printElements($array);
        echo self::CLOSE_TAG;
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
        $this->printObject($this, "");
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
            $name = $attributes['name'];
            $class = (string)$attributes['class'];
            switch ($type) {
                case "Object":  $object = new $class();
                                $object->readStructure($element);
                                $innerArray["{$name}"] = $object;
                                break;
                case "Array":   $innerArray["{$name}"] = $this->getArrayStructure($element); break;
                case "Value":   $innerArray["{$name}"] = (string)trim($element); break;
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
            $name = $attributes['name'];
            $class = (string)$attributes['class'];
            switch ($type) {
                case "Object": $object = new $class();
                               $object->readStructure($element);
                               $this->{$name} = $object; break;
                case "Array":  $this->{$name} = $this->getArrayStructure($element); break;
                case "Value":  $this->{$name} = (string)trim($element); break;
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