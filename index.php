<?php
include 'inc/functions.php';
session_start();
?>
<!DOCTYPE html>
<html>
    
    <head>
        <title> Team Project </title>
        <style>
            @import url("css/styles.css");
        </style>
        <script>
function myFunction(info)
{
alert(info);
}
</script>
    </head>
    <body>
        <h2>PC Parts Store</h2>
        <form action="index.php" method="post">
          Model:<br>
          <input type="text" name="Model"><br>
          Cores:<br>
          <input type="text" name="Cores"><br>
          Unlocked(Yes or No):<br>
          <input type="text" name="Unlocked">
          <input type="submit" value="Submit">
        </form>
        <!--<img src="img/cherry.png" alt="cherry" title="Cherry" width="70" />-->
        <?php
            ///////////////////////////////////////////////////////////////////////
            //This is the example provided on ilearn modified to fit this project
            
        
        $orderCPUBy = array('Model', 'Unlocked', 'Cores', 'Price','Info');
        $orderGPUBy = array('Model', '3DMark', 'Cores', 'Price');//include this in the GPUY array
        $ordercpu = "Model";
        $ordergpu = "Model";
        if (isset($_GET['orderCPUBy']) && in_array($_GET['orderCPUBy'], $orderCPUBy)) {
            $ordercpu = $_GET['orderCPUBy'];
        }
        if (isset($_GET['orderGPUBy']) && in_array($_GET['orderGPUBy'], $orderGPUBy)) {
            $ordergpu = $_GET['orderGPUBy'];
        }
        //Variables used to filter the results
        $model = $_POST["Model"];
        $unlocked = $_POST["Unlocked"];
        $cores = $_POST["Cores"];
        //The new connection setup for Heroku
        ////////////////////////////////////////////////////////////
        $connUrl = getenv('JAWSDB_MARIA_URL');
        //$connUrl = "mysql://ikxzumlxt0a0uq9x:qendeuysn1eho7ym@thzz882efnak0xod.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/s1vxerk2jlp6h9j1";
        $hasConnUrl = !empty($connUrl);

        $connParts = null;
        if ($hasConnUrl) {
            $connParts = parse_url($connUrl);
        }

        //var_dump($hasConnUrl);
        $host = $hasConnUrl ? $connParts['host'] : getenv('IP');
        $dbname = $hasConnUrl ? ltrim($connParts['path'],'/') : 'TeamProject';
        $username = $hasConnUrl ? $connParts['user'] : getenv('C9_USER');
        $password = $hasConnUrl ? $connParts['pass'] : '';

        $dbConn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $dbConn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //return new PDO("mysql:host=$host;dbname=$dbname", $username, $password);//For some reason this line from Jasons exampled isnt working for me
        //////////////////////////////////////////////////////////////
        $sql =  'SELECT * FROM Processors WHERE Model LIKE "'.$model.'%" AND Unlocked LIKE "'.$unlocked.'%" AND Cores LIKE "'.$cores.'%" ORDER BY ' .$ordercpu;

        $stmt = $dbConn -> prepare ($sql);
        $stmt -> execute (  array ( ':id' => '1')  );
        if ($stmt->rowCount() > 0) {
            //Creates a table
            $table_str.= '<div class="column">';
            $table_str.='CPU';
            $table_str.='<table>';
            $table_str.='<th>'.  '<a href="?orderCPUBy=Model">Model</a>'.'</th>';
            $table_str.='<th>'. '<a href="?orderCPUBy=Unlocked">Unlocked</a>'.'</th>';
            $table_str.='<th>'. '<a href="?orderCPUBy=Cores">Cores</a>'.'</th>';
            $table_str.='<th>'. '<a href="?orderCPUBy=Price">Price</a>'.'</th>';

        while ($row = $stmt -> fetch())  {
            $table_str.='<tr>';
            $table_str.='<td>'.$row['Model'].'</td>'.'<td>'.$row['Unlocked'].'</td>'.'<td>'.$row['Cores'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str.= '<td><form action="" method="post"><input type="submit"" value = "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
            $table_str.='<td>';
            $table_str.='<input type="button" onclick="myFunction(';
            $table_str.="'";
            $table_str.=$row['Info'];//This was difficult for some reason, we need to add the column to this and then pull that instead of model
            $table_str.="'".')" value="Info" />';
            $table_str.='</td>';
            $table_str.='</tr>';
        }
        $table_str.='</table>';
        $table_str.-'</div>';
        //$table_str2.='</div>';
        $sql =  'SELECT * FROM GPU WHERE Model LIKE "'.$model.'%" ORDER BY ' .$ordergpu;
        $stmt = $dbConn -> prepare ($sql);
        $stmt -> execute (  array ( ':id' => '1')  );
        if ($stmt->rowCount() > 0) {
            //Creates the table headers
            $table_str2.='<div class="column">';
            $table_str2.= 'Graphics cards';
            $table_str2.='<table>';
            $table_str2.='<th>'.  '<a href="?orderGPUBy=Model">Model</a>'.'</th>';
            $table_str2.='<th>'. '<a href="?orderGPUBy=3DMark">3DMark</a>'.'</th>';
            $table_str2.='<th>'. '<a href="?orderGPUBy=Cores">Cores</a>'.'</th>';
            $table_str2.='<th>'. '<a href="?orderGPUBy=Price">Price</a>'.'</th>';

        while ($row = $stmt -> fetch())  {
            //Creates the table rows
            $table_str2.='<tr>';
            $table_str2.='<td>'.$row['Model'].'</td>'.'<td>'.$row['3DMark'].'</td>'.'<td>'.$row['Cores'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str2.= '<td><form action="" method="post"><input type="submit"" value = "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
            $table_str2.='<td>';
            $table_str2.='<input type="button" onclick="myFunction(';
            $table_str2.="'";
            $table_str2.=$row['Info'];//This was difficult for some reason, we need to add the column to this and then pull that instead of model
            $table_str2.="'".')" value="Info" />';
            $table_str2.='</td>';
            $table_str2.='</tr>';
        }
        $table_str2.='</table>';
        $table_str2.='</div>';
        }
        
        
        
        
        $table_str3.='</div>';
        $sql =  'SELECT * FROM GPU WHERE Model LIKE "'.$model.'%" ORDER BY ' .$ordergpu;
        $stmt = $dbConn -> prepare ($sql);
        $stmt -> execute (  array ( ':id' => '1')  );
        if ($stmt->rowCount() > 0) {
            //Creates a table headers
            $table_str3.='<div class="column">';
            $table_str3.='Graphics cards';
            $table_str3.='<table>';
            $table_str3.='<th>'.  '<a href="?orderGPUBy=Model">Model</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderGPUBy=3DMark">3DMark</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderGPUBy=Cores">Cores</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderGPUBy=Price">Price</a>'.'</th>';

        while ($row = $stmt -> fetch())  {
            //fills the table rows
            $table_str3.='<tr>';
            $table_str3.='<td>'.$row['Model'].'</td>'.'<td>'.$row['3DMark'].'</td>'.'<td>'.$row['Cores'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str3.= '<td><form action="" method="post"><input type="submit"" value = "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
            $table_str3.='<td>';
            $table_str3.='<input type="button" onclick="myFunction(';
            $table_str3.="'";
            $table_str3.=$row['Info'];//This was difficult for some reason, we need to add the column to this and then pull that instead of model
            $table_str3.="'".')" value="Info" />';
            $table_str3.='</td>';
            $table_str3.='</tr>';
        }
        $table_str3.='</table>';
        //$table_str3.='</div>';
        echo '<div class="row">';
        echo $table_str;
        echo $table_str2;
        echo $table_str3;

        echo '</div>';
        }
        }
        else {
        echo "No data found";
        }
        ?>
        </div>
    </body>
</html>