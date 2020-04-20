<?php

    $servername = "localhost";
    $username = "rievrs";
    $password = "";
    $dbname = "news";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        
    } else {
        echo "\nConectado com susccesso\n";
    }
    $aspas = "'";
    $aspasDuplas = '"';
    $title = str_replace($aspas, $aspasDuplas, $title); //alguns títulos tem aspas simples que atrapalham a query do banco de dados.

    $sql = "INSERT INTO posts (title, url)
    VALUES ('$title', '$url')";

    if ($conn->query($sql) === TRUE) {
        echo "\nCriado com succeso\n";
    } else {
        echo "\nError: " . $sql . " " . $conn->error;
    }

$conn->close();
?>