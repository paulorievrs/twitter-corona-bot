<?php

    $servername = "localhost";
    $username = "rievrs";
    $password = "";
    $dbname = "news";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("\nConnection failed: " . $conn->connect_error);
        
    } else {
      
    $sql = "SELECT title FROM posts";
        
    $result = $conn->query($sql);
    $count = 0;
    $arrayTitle = [];

        
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $arrayTitle[$count] = $row["title"];
                $count++;
            }
        } else {
            echo "\nNenhum título encontrado";
        }

        
    }