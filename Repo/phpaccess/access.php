<?php
    try {
        $db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ="."C:\Users\AssessorTrain\Desktop\BOE Access Data\LosAngeles.mdb", "", "");
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    } 

?>