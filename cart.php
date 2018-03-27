<?php
include 'inc/functions.php';
session_start();
?>
<html>
    <head>
        <title> Team Project </title>
        <style>
            @import url("css/styles.css");
        </style>
    </head>
    <body>
        <h2>PC parts store cart</h2>
        <?php
        $total=0;
            $table_str.='<div class="column">';
            $table_str.='<table>';
            $table_str.='<th>Model</th>';
            $table_str.='<th>Price</th>';
        foreach($_SESSION['cart'] as $product){
            $pieces = explode("*",$product);
            $table_str.='<tr>';
            $table_str.='<td>'.  $pieces[0].'</td>';
            $table_str.='<td>$'.  $pieces[1].'</td>';
            $table_str.='</tr>';
            
            //echo '<h5>'.$pieces[0].'---------------$'.$pieces[1].'<h5>';
            $total = $total + (int)$pieces[1];
            //echo '<h5>'.str_replace("*", " ------ ",$product).'<h5>';
        }
        $table_str.='<td>Total:</td>';
        $table_str.='<td>$'.$total.'</td>';
        $table_str.='</table>';
        echo $table_str;
        echo "<a href=index.php class='button'>Keep Shopping</a>";

        //var_dump($_SESSION['cart']);
        ?>
    </body>
</html>