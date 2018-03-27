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
        echo "<a href=cart.php class='button'>Shopping Cart</a>";
        $num=0;
        $_SESSION['cart'] = array();
            ///////////////////////////////////////////////////////////////////////
            //This is the example provided on ilearn modified to fit this project
            //echo 'POST: '.$_POST['Add'];
        //$_SESSION['tester'] = 'This is a test on index.php at line 36';
        $orderCPUBy = array('Model', 'Unlocked', 'Cores', 'Price','Info');
        $orderGPUBy = array('Model', '3DMark', 'Cores', 'Price');//include this in the GPUY array
        $orderMBBy = array('Model','Socket','RAM','Price');
        $ordercpu = "Model";
        $ordergpu = "Model";
        $ordermb = "Model";
        if (isset($_GET['orderCPUBy']) && in_array($_GET['orderCPUBy'], $orderCPUBy)) {
            $ordercpu = $_GET['orderCPUBy'];
        }
        if (isset($_GET['orderGPUBy']) && in_array($_GET['orderGPUBy'], $orderGPUBy)) {
            $ordergpu = $_GET['orderGPUBy'];
        }
        if(isset($_GET['orderMBBy'])&& in_array($_GET['orderMBBy'], $orderMBBy)){
            $ordermb = $_GET['orderMBBy'];
        }
        if (!isset($_POST['Add'])) {
           // $_SESSION['tester2'] = 'This is a test at line 53';
            echo 'Session: '.$_SESSION['tester2'];
            //var_dump($_SESSION['tester2']);
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
            $table_str.='<form action="" method="post">';
        while ($row = $stmt -> fetch())  {
            $table_str.='<tr>';
            $table_str.='<td>'.$row['Model'].'</td>'.'<td>'.$row['Unlocked'].'</td>'.'<td>'.$row['Cores'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str.= '<td>
            <input type="checkbox" name=products[] value='.$row['Model']."*".$row['Price'].'>
            <input type="submit"" name=product value= "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
            $table_str.='<td>';
            $table_str.='<input type="button" onclick="myFunction(';
            $table_str.="'";
            $table_str.=$row['Info'];//This was difficult for some reason, we need to add the column to this and then pull that instead of model
            $table_str.="'".')" value="Info" />';
            $table_str.='</td>';
            $table_str.='</tr>';
            $num=$num+1;
        }
        //$table_str.='</form>';
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
            //$table_str2.='<form action="" method="post">';
        while ($row = $stmt -> fetch())  {
            //Creates the table rows
            $table_str2.='<tr>';
            $table_str2.='<td>'.$row['Model'].'</td>'.'<td>'.$row['3DMark'].'</td>'.'<td>'.$row['Cores'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str2.= '<td><input type="checkbox" name="products[]" value="'.$row['Model']."*".$row['Price'].'">
            <input type="submit" name="product" value= "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
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
        $sql =  'SELECT * FROM Motherboard ORDER BY ' .$ordermb;
        $stmt = $dbConn -> prepare ($sql);
        $stmt -> execute (  array ( ':id' => '1')  );
        if ($stmt->rowCount() > 0) {
            //Creates a table headers
            $table_str3.='<div class="column">';
            $table_str3.='Motherboards';
            $table_str3.='<table>';
            $table_str3.='<th>'.  '<a href="?orderMBBy=Model">Model</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderMBBy=Socket">Socket</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderMBBy=RAM">RAM</a>'.'</th>';
            $table_str3.='<th>'. '<a href="?orderMBBy=Price">Price</a>'.'</th>';

        while ($row = $stmt -> fetch())  {
            //fills the table rows
            $table_str3.='<tr>';
            $table_str3.='<td>'.$row['Model'].'</td>'.'<td>'.$row['Socket'].'</td>'.'<td>'.$row['RAM'].'</td>'.'<td>'.$row['Price'].'</td>';
            $table_str3.= '<td><input type="checkbox" name="products[]" value="'.$row['Model']."*".$row['Price'].'">
            <input type="submit" name="product" value= "Add to Cart"></td>';//Adds the button to add the item to the users sessions, I have not yet implemented the session
            $table_str3.='<td>';
            $table_str3.='<input type="button" onclick="myFunction(';
            $table_str3.="'";
            $table_str3.=$row['Socket'];//This was difficult for some reason, we need to add the column to this and then pull that instead of model
            $table_str3.="'".')" value="Info" />';
            $table_str3.='</td>';
            $table_str3.='</tr>';
            
        }
        $table_str3.='</form>';
        $table_str3.='</table>';
        //$table_str3.='</div>';
        echo '<div class="row">';
        echo $table_str;
        echo $table_str2;
        echo $table_str3;
        //echo $_SESSION['tester2'];
        echo '</div>';
        foreach($_POST['products'] as $product) //loop through the checkboxes
        {
            // add to cart
            
            array_push($_SESSION['cart'],$product);
            //echo array_values($_SESSION['tester2']);
            //echo $product;
            //echo $product['Model'];
        }
        var_dump($_SESSION['cart']);
        echo "<a href=cart.php class='button'>Shopping Cart</a>";
        //echo $_SESSION['tester2'];
        }
        }
        else {
        echo "No data found";
        }
        ?>
        </div>
    </body>
</html>