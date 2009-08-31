<?php

/************************************************************************
             Serializer - Copyright 2009 Dennis Cohn Muroy

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

/**
 * This class contains the base methods for performing the serialization /
 * unserialization of an object.
 * @author Dennis Cohn Muroy <dennis.cohn@pucp.edu.pe>
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.3
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 * @subpackage Base
 * @abstract
 */
abstract class Serializer
{

    const SERIALIZER_OBJECT = "Object";

    const SERIALIZER_ARRAY = "Array";

    const SERIALIZER_VALUE = "Value";

    /**
     * This is the method that must be called in order to convert this object
     * into an serialized structure.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     */
    public final function serialize()
    {
        $this->writeHeader();
        $this->serializeObjectStructure();
        $this->writeFooter();
    }

    /**
     * This is the method that must be called in order to unserialize a previously
     * serialized object.
     * @author Dennis Cohn Muroy
     * @access public
     * @final
     * @param string $file File or url that contains the serialized Structure.
     */
    public final function unserialize( $file )
    {
        $this->loadRequiredModules();
        $structure = $this->retrieveStructure($file);
        $this->readStructure($structure);
    }

    /**
     * This is the method that must be called in order to serialize an object.
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     */
    protected final function serializeObjectStructure()
    {
        $attributes = get_object_vars($this);
        $this->writeArray($attributes);
    }

    /**
     * Converts an object into a serialized structure
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     * @param Object $value Object to be converted to a serialized structure
     */
    protected final function writeObject($value)
    {
        $funct = "serializeObjectStructure";
        if (is_callable(array($value,$funct),true) !== FALSE) {
            $value->{$funct}();
        }
    }

    /**
     * Indicates if the parameter value is an object, an array or any other value
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     * @param mixed $value
     * @return String
     */
    protected final function getType ($value)
    {
        if (is_object($value)) {
            return self::SERIALIZER_OBJECT;
        } else if (is_array($value)) {
            return self::SERIALIZER_ARRAY;
        } else {
            return self::SERIALIZER_VALUE;
        }
    }

    /**
     * Retrieves the datatype of the parameter value
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     * @param mixed $value
     * @return String
     */
    protected final function getDataType ($value)
    {
        if (is_object($value)) {
            return get_class($value);
        } else if (is_array($value)) {
            return "array";
        } else {
            return gettype($value);
        }
    }

    /**
     * Converts a list of attributes with is assigned values into serialized nodes
     * @author Dennis Cohn Muroy
     * @access protected
     * @final
     * @param mixed $elements List of attributes with its assigned values
     */
    protected final function writeArray($elements)
    {
        // TODO: Validate if last element of an array
        $totalElements = count($elements);
        $currentItem = 0;
        foreach($elements as $key => $value) {
            $type = $this->getType($value);
            $class = $this->getDataType($value);
            $lastElement = ++$currentItem == $totalElements;
            // TODO: Validate if inside an array
            $this->writeNodeStart($key, $type, $class);
            $action = "write".$type;
            $this->{$action}($value);
            $this->writeNodeEnd($key, $type, $class);
        }
    }

    /**
     * Writes a string before the object is serialized
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     */
    protected abstract function writeHeader();

    /**
     * Writes a string after the object is serialized
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     */
    protected abstract function writeFooter();

    /**
     * Writes the open string before the node content us writen
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param String $key
     * @param String $type
     * @param String $class
     */
    protected abstract function writeNodeStart($key, $type = "", $class = "");

    /**
     * Writes the close string after the node content is writen
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param String $key
     * @param String $type
     * @param String $class
     */
    protected abstract function writeNodeEnd($key, $type = "", $class = "");

    /**
     * Loads any PHP module that is necessary for the unserialization process.
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     */
    protected abstract function loadRequiredModules();

    /**
     * Retrieves the serialized structure from a file or an url
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param String $file The file name or URL
     */
    protected abstract function retrieveStructure($file);

    /**
     * Writes the value of a serialized node.
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param Object $value Value to be displayed in a serialized node
     */
    protected abstract function writeValue($value);

    /**
     * This function loads values of the node into an array.
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param mixed $node Node of the serialized structure
     * @return array
     */
    protected abstract function getArrayStructure($node);

    /**
     * This function loads values of the node into the object's attributes.
     * @author Dennis Cohn Muroy
     * @access protected
     * @abstract
     * @param mixed $node Node of the serialized structure
     */
    protected abstract function readStructure($node);
}

?>
