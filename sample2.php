<?php

/************************************************************************
        sample2.php - Copyright 2009 Dennis Cohn Muroy

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

require_once('XMLserializer.php');

class InnerObject extends XMLserializer
{
    protected $_id;
    protected $_name;
    protected $_array;

    public function __construct()
    {
        $this->_id = "";
        $this->_name = "";
        $this->_array = array();
    }

    public function loadSampleData($name = __CLASS__)
    {
        $this->_id = md5($name);
        $this->_name = $name;
        $this->_array = array("FirstElement"=>"item","SecondElement"=>array("subitem1","subitem2"));
    }

    public function __destruct()
    {
        unset($this->_id);
        unset($this->_name);
    }
}

class MainObject extends XMLserializer
{
    protected $_id;
    protected $_name;
    protected $_object;
    protected $_array;

    public function __construct()
    {
        $this->_id = "";
        $this->_name = "";
        $this->_object = new InnerObject();
        $this->_array = array();
    }

    public function loadSampleData($name = __CLASS__)
    {
        $this->_id = md5($name);
        $this->_name = $name;
        $this->_object->loadSampleData();
        $this->_array = array(new InnerObject(), new InnerObject());
        $this->_array[0]->loadSampleData("inner1");
        $this->_array[1]->loadSampleData("inner2");
    }

    public function __destruct()
    {
        unset($this->_id);
        unset($this->_name);
        unset($this->_object);
    }
}

if (isset($_GET["write"])) {
    $xml = new MainObject();
    $xml->loadSampleData();
    $xml->writeXML();
} else {
    $xml = new MainObject();
    echo "o2xml2o XMLserializer sample file:<br /><br />";
    echo "New Object:<br />";
    var_dump($xml);
    $path = $_SERVER['PHP_SELF'];
    $xml->readXML("http://localhost{$path}?write");
    echo "<br /> <br />";
    ?>
    <div style="float:left; width:49%;">
    Loaded Object:<br />
    <pre>
    <?php
    var_dump($xml);
    ?>
    </pre>
    </div>
    <div style="float:right; width:49%;">
    Expected Object:<br />
    <pre>
    <?php
    $xml = new MainObject();
    $xml->loadSampleData();
    var_dump($xml);
    ?>
    </pre>
    </div>
    <?php
}
