<?php

/************************************************************************
        XML-Serializer.php - Copyright 2010 Dennis Cohn Muroy

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

require_once('../o2xml2o/XMLserializer.php');

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


$xml = new MainObject();
if (isset($_GET["write"])) {
    $xml->loadSampleData();
    $xml->writeXML();
} else {

?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <title>XML Serializer Example</title>
        <link type="text/css" rel="stylesheet" href="./style.css" />
    </head>
    <body>
        <h1>o2xml2o XML-serializer Sample</h1>
        <h2>Base Object</h2>
        <pre><?php var_dump($xml); ?></pre>
        <h2>Loaded Object</h2>
        <?php $xml->loadSampleData(); ?>
        <pre><?php var_dump($xml); ?></pre>
        <p>Serialized Value: <?php $serial1 = serialize($xml); echo $serial1; ?></p>
        <h2>Imported Object</h2>
        <?php
            $xml = new MainObject();
            $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?write";
        ?>
        <pre><?php $xml->readXML($url); var_dump($xml); ?></pre>
        <p>Serialized Value:<br /> <?php $serial2 = serialize($xml); echo $serial2; ?></p>
        <h2>Result:</h2>
        <p>
            Both serialized values
        <?php
            if (trim($serial1) == trim($serial2)) {
                echo "MATCH";
            } else {
                echo "DO NOT MATCH<br />";
            }
        ?>
        </p>
        <hr />
        Inicio [<a href="http://<?php echo $_SERVER['SERVER_NAME'];?>">&gt;&gt;</a>]
    </body>
</html>
<?php
}
?>