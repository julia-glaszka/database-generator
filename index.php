<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="src/style.css">
</head>

<body>
    <div class="section">
        <div class="container">
            <nav class="panel">
                <p class="panel-heading">
                    Add data
                </p>
                <div class="panel-block">
                    <div class="field has-addons">
                        <p class="control is-expanded">
                            <input class="input" type="text" placeholder="Name of database">
                        </p>
                        <p class="control is-expanded">
                            <input class="input" type="text" placeholder="Name of table">
                        </p>
                        <p class="control">
                            <a class="button">
                                Generate
                            </a>
                        </p>
                    </div>
                </div>

                <div class="panel-block is-active">
                <textarea class="textarea" placeholder="['id', 'INT AUTO_INCREMENT PRIMARY KEY'], 
['user', 'VARCHAR(32)'],
['password', 'VARCHAR(32)'],
['birth_date', 'DATE']
        "></textarea>
                </div>
                <div class="panel-block">
    <button class="button is-link is-outlined is-fullwidth">
      Generate random data
    </button>
  </div>



            </nav>




            <?php 
            set_time_limit(0);
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
class DataGenerator {
    private $SERVERNAME;
    private $USERNAME;
    private $PASSWORD;
    private $DBNAME;
    private $AMOUNT;
    private $TABLENAME;
    private $conn ;
    private $scheme;
    
    private $types = [
        'AUTO_INCREMENT',
        'POINT',
        'DATETIME',
        'TIMESTAMP',
        'INT',
        'YEAR',
        'DECIMAL',
        'FLOAT',
        'DOUBLE',
        'VARCHAR',
        'TEXT',
        'DATE',
        'TIME'    
    ];
    private $errorMessages = [];
    private $reports = [];
        /**
     * Class constructor.
     */
    public function __construct($SERVERNAME = 'localhost', $USERNAME = 'root', $PASSWORD = '', $DBNAME = "baza", $AMOUNT = 100, $TABLENAME = 'tabela', $SCHEME = [])
    {
        $this->SERVERNAME = $SERVERNAME;
        $this->USERNAME = $USERNAME;
        $this->PASSWORD = $PASSWORD;
        $this->DBNAME = $DBNAME;
        $this->AMOUNT = $AMOUNT;
        $this->TABLENAME = $TABLENAME;
        $this->scheme = $SCHEME;

        try {
            $this->conn = new mysqli($this->SERVERNAME, $this->USERNAME, $this->PASSWORD);


            
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
        } catch (php_network_getaddressess $e) {
            throw $e->getMessage();
        }
    }


    function initDatabase() {
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Create database
        $sql = "CREATE DATABASE IF NOT EXISTS $this->DBNAME;";

        try {
            $a = $this->conn->query($sql);

        } catch (mysqli_sql_exception $e) {
            $err = $e->getMessage();
            array_push($this->errorMessages, $err);
            $this->printError($err);
        }
            // echo "Database created successfully";
     
    }
    function printError($e) {
        echo '<p class="has-text-danger">'.$e."</p>";
    }
    function printAllErrorMessages() {
        if($this->errorMessages) {
            echo '<div class="notification is-danger">
            <div class="title">An errors occured</div>';
            foreach ($this->errorMessages as $message) {
                echo '<div class="content">'.$message.'</div>';
            }
            
           echo' </div>';
    
        }
       
    }
function createTable () {
    $sql = " CREATE TABLE IF NOT EXISTS $this->DBNAME.$this->TABLENAME (";
    foreach ($this->scheme as $column) {

        foreach ($column as $props) {
        $sql.=$props.' ';    
        }
        $sql.=', ';
    }
    $sql = substr($sql, 0, -2);
    $sql.= ');';
    // echo $sql;
        try {
             $this->conn->query($sql);
        } catch (mysqli_sql_exception $e) {
            $err = $e->getMessage();
            array_push($this->errerrorMessagesors, $err);
            $this->printError($err);
        } catch (php_network_getaddressess $e) {
            $err = $e->getMessage();
            array_push($this->errorMessages, $err);
            $this->printError($err);
        }
    
    return $sql;
}

    function randDate($min = 1940) {
        $y= mt_rand($min, date("Y"));
        $m= mt_rand(1, 12);
        $d= mt_rand(1, 28);
        return (($y/10)<1?$this->addNullAtStart($y):$y) . "-". (($m/10)<1?$this->addNullAtStart($m):$m) ."-". (($d/10)<1?$this->addNullAtStart($d):$d) ;
    }

    function randBool() {
        return ((bool)rand(0,1))?"1":"0";
    }
    function randPoint() {
        return "ST_GeomFromText('POINT(2489 2450)')";
    }
    function randTime () {
        $h = rand(0,23);
        $m =  rand(0,59);
        $s =  rand(0,59);
        return  (($h/10)<1?$this->addNullAtStart($h):$h) . ":". (($m/10)<1?$this->addNullAtStart($m):$m) .":". (($s/10)<1?$this->addNullAtStart($s):$s) ;
    }
    function addNullAtStart($num) {
        return "0".$num;
    }
    function randWord($length=4){
        return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,$length);
    }
    function randDateTime() {
        return $this->randDate(). " ". $this->randTime();
    }
    function randTimestamp() {
        return $this->randDate(1971). " ". $this->randTime();
    }
    function randInt() {
        return rand(-1000, 1000);
    }
   
    function randDouble() {
        return round(rand(-1000, 1000)/6, 2);
    }


function randYear() {
    return rand(1970,2100);
}






    function parseTypes (){
        $typesx = [];
        foreach ($this->scheme as $prop) {
            // foreach ($prop as $val) {
             foreach ($this->types as $type) {
                 if(preg_match("/$type/im", $prop[1])) {
                    array_push($typesx, $type);
                 break;
                 }

             }
            // }

        }
     
        return $typesx;
    }

    function prepareInsert() {
        $sql = "INSERT INTO $this->DBNAME.$this->TABLENAME (";
        foreach ($this->scheme as $props) {
            if(!preg_match("/AUTO_INCREMENT/im", $props[1])){
                $sql.=$props[0]. ', ';

            }
        }
        $sql= substr($sql, 0, -2);
        $sql.= ") VALUES (";
        return $sql;
    }


    function generateAndInsertData() {
        $time = microtime(true);
        $typesx = $this->parseTypes();
        $insert = $this->prepareInsert();
        if($typesx) {
            for ($i=0, $p =1; $i < $this->AMOUNT; $i++, $p++) { 
                $sql = $insert;
               
                
                foreach ($typesx as $type) {
                    $sql.= $this->generateDataByType($type);
                }
                $sql=  substr($sql, 0, -2);
                $sql.= ');';
                echo '<b>'.($p/$this->AMOUNT)*100 . "%  </b> ";
                echo $sql. "<br/>";

                try {
                    $this->conn->query($sql);
    
                } catch (mysqli_sql_exception $e) {
                    $err = $e->getMessage();
                    array_push($this->errorMessages, $err);
                    $this->printError($err);
                }
            }
        }
        $this->printTime($time);
        echo "<hr></br>";
    }

    function generateAndInsertMultipleData($amountInOneInsert = 10 ) {
        $time = microtime(true);
        $typesx = $this->parseTypes();
        $insert = $this->prepareInsert();
        $offset = $this->AMOUNT%$amountInOneInsert;
        $iterations = floor($this->AMOUNT/$amountInOneInsert);
        if($typesx) {
            for ($i=0, $p =0; $i < $iterations; $i++, $p+=$amountInOneInsert) { 
                
                $sql = $insert;
                if ($this->AMOUNT-($i*$amountInOneInsert)<$amountInOneInsert) {
                    for ($s = 0; $s <$offset; $s++){
                        foreach ($typesx as $type) {
                            $sql.= $this->generateDataByType($type);
                        }
                        $sql=  substr($sql, 0, -2);
                        $sql.= '), (';
    
                    }
                    $sql=  substr($sql, 0, -3);
                } else {
                for ($s = 0; $s <$amountInOneInsert; $s++){
                    foreach ($typesx as $type) {
                        $sql.= $this->generateDataByType($type);
                    }
                    $sql=  substr($sql, 0, -2);
                    $sql.= '), (';

                }
                $sql=  substr($sql, 0, -3);
               
            }
            $sql.=';';
            echo '<b>'.($p/$this->AMOUNT)*100 . "%  </b> ";
            echo $sql. "<br/>";
                try {
                    $this->conn->query($sql);
                } catch (mysqli_sql_exception $e) {
                    $err = $e->getMessage();
                    array_push($this->errorMessages, $err);
                    $this->printError($err);
                }
               

            }
        }
        //$time, $amountInOneInsert, $amountOfData)
        $timeM =  microtime(true) - $time;

        $this->makeReport($timeM, $amountInOneInsert, $this->AMOUNT);
        $this->printTime($timeM);
        $this->printSuccessRate($iterations+1);
        echo "<hr></br>";
    }


    function close() {
        $this->conn->close(); 
   }
   function printTime($time) {
       echo '<p class="has-text-success	"> Exectuted in:'. $time .'s </p>';
   }
   function printSuccessRate($iterations){
       $successRate = (($iterations-sizeof($this->errorMessages))/$iterations);
    echo '<h2 class="is-size-3">Success rate:';
    if ($successRate>0.98) {
        echo '<b class="has-text-success"> '. $successRate .'</b>';
    } else {
        echo '<b class="has-text-danger"> '. $successRate .'</b>';
    }
    echo '</h2>';
   }
    function generateDataByType($type) {
        // echo 'Debug: generateType(): ';
        // echo $type .'<br/>';
        if($type) {
            switch ($type) {
                case "AUTO_INCREMENT":
                    //nothing to do
                break;
                case 'POINT':
                    return $this->randPoint().", ";
                break;
                case 'DATETIME':
                    return $this->quote($this->randDateTime());
                break;
                case 'TIMESTAMP': 
                    return $this->quote($this->randTimestamp());
                break;
                case 'INT': 
                    return $this->quote($this->randInt());
                break;
                case 'YEAR':
                    return $this->quote($this->randYear());
                break;
                case 'DECIMAL':
                case 'FLOAT':
                case 'DOUBLE':
                    return $this->quote($this->randDouble());
                break;
                case   'VARCHAR':
                case 'TEXT':
                    return $this->quote($this->randWord());
                break;
                case 'DATE':
                    return $this->quote($this->randDate());
                break;
                case 'TIME':
                    return $this->quote($this->randTime());
                break;
                default:
                    return false;
            break;

            }

        }

    }
    function makeReport($time, $amountInOneInsert, $amountOfData) {
        if($time<=0) {
            $time = 0.00000000001;
        }
        $efficiency = $amountOfData/$time;
       array_push($this->reports, [
        "amountOfData" => $amountOfData,
        "amountInOneInsert" => $amountInOneInsert,
           "time"=> $time,
           "efficiency"=> $efficiency
          
       ]);
    }
    function printReports() {
        echo ' <table class="table">
        <thead>
          <tr>
            <th><abbr title="Position">No.</abbr></th>
            <th><abbr title="Played">Ilość dodanych rekordów</abbr></th>
            <th><abbr title="Won">Ilość w jednym insercie</abbr></th>
            <th><abbr title="Drawn">Czas [s]</abbr></th>
            <th><abbr title="Lost">Efektywność [ilosc rekordow na s]</abbr></th>
          </tr>
        </thead>
       
        <tbody>';
        $arrSize = sizeof($this->reports);
        $i = 1;
        foreach ($this->reports as $report) {
         
            echo "
            <tr>
              <th>$i</th>
              <td>".
                $report["amountOfData"].
              "</td>
              <td>".
                $report['amountInOneInsert'].
              "</td>
              <td>".
                $report['time'].
                "</td>
              <td>" . 
              $report['efficiency'].
              "</td>
            </tr>";
            $i++;
        }
       echo' </tbody> </table>';
    }



    function quote($val) {
        return '"'.$val.'", ';
    }
    function run () {
        $this->initDatabase();
    $this->createTable();

    // foreach ($this->scheme as $value) {
    //     // echo $value[1]. "*****";
    // }
        // $this->generateAndInsertMultipleData(10);

        // $this->generateAndInsertMultipleData(20);

        // // $this->generateAndInsertMultipleData(50);
        // $this->generateAndInsertMultipleData(100);
        // $this->generateAndInsertMultipleData(200);
        // $this->generateAndInsertMultipleData(30);
        // $this->generateAndInsertMultipleData(1000);
        // $this->generateAndInsertMultipleData(2000);
        $this->generateAndInsertMultipleData(2000);
        // $this->generateAndInsertMultipleData(120);

        $this->printReports();
        $this->close();
        $this->printAllErrorMessages();
    }

}

// $data = array(
//     ["id", "INT AUTO_INCREMENT PRIMARY KEY"],
//     ["usffeir", "VARCHAR(30)"],
//     ["password", "VARCHAR(30)"],
//     ["birth_date", "DATE"]);

// $db = new DataGenerator('localhost', 'root', '', 'ba124', 10, 'tabelka', $data);
// $db->run();


$datax = array(
    ["id", "INT AUTO_INCREMENT PRIMARY KEY"],
    ["user", "VARCHAR(30)"],
    ["password", "VARCHAR(30)"],
    ["birth_date", "DATE"],
    ["location", "POINT"],
    ["birth_year", "YEAR"],
    ["logout", "TIMESTAMP"],
    ["loggedIn", "DATETIME"],
    ["age", "INT"],
    ["address", "TEXT"]
);

$dbx = new DataGenerator('localhost', 'root', '', 'korporacja', 12000, 'dane_osobowe', $datax);
$dbx->run();

// $dataz = array(
//     ["id", "INT AUTO_INCREMENT PRIMARY KEY"],
//     ["name", "VARCHAR(30)"],
//     ["type", "VARCHAR(30)"],
//     ["category", "DATE"],
//     ["prod_year", "YEAR"],
//     ["value", "DOUBLE"],
//     ["sale","FLOAT"],
//     ["mytime", "TIME"],
//     ["location", "POINT"],
//     ["delivery_time","TIME"]
// );

// $dbz = new DataGenerator('localhost', 'root', '', 'korporacja', 10, 'produkty', $dataz);
// $dbz->run();

// $dbc = array (

// ["id", "INT AUTO_INCREMENT PRIMARY KEY"],
// ["user", "VARCHAR(30)"],
// ["password", "VARCHAR(30)"],
// ["birth_date", "DATE"],
// ["location", "POINT"],
// ["location2", "FLOAT"],
// ["location3", "POINT"],
// ["birth_year", "YEAR"],
// ["logout", "TIMESTAMP"],
// ["login", "TIME"],
// ["loggedIn", "DATETIME"],
// ["age", "INT"],
// ["address", "TEXT"],
// ["name", "VARCHAR(40)"],
// ["type", "VARCHAR(30)"],
// ["category", "DATE"],
// ["prod_year", "YEAR"],
// ["value", "DOUBLE"],
// ["sale","FLOAT"],
// ["loggedIn2", "DATETIME"],
// ["size", "INT"],
// ["delivery_time","TIME"],
// ["joined","YEAR"],
// ["costs","INT"],
// ["burns","DECIMAL"]
// );

// $dbca = new DataGenerator('localhost', 'root', '', 'korporacja', 10, 'duzo', $dbc);
// $dbca->run();
?>





        </div>
    </div>

</body>

</html>