<?php
/************************************************************************
        JSON_Sample.php - Copyright 2009 Dennis Cohn Muroy

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

require_once 'o2xml2o/JSONserializer.php';

/**
 * This class is a sample of how to use the JSONSerializer class.
 * @author Dennis Cohn Muroy <dennis.cohn@pucp.edu.pe>
 * @copyright Copyright (c) 2009, Dennis Cohn Muroy
 * @version 0.1
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3
 * @package o2xml2o
 */
class JSON_Sample extends JSONSerializer {

    protected $atrributeValue1 = "value1";
    protected $atrributeValue2 = "value2";
    protected $atrributeArray1 = array("item1", "item2");
    
}
?>
