<?php

$file = "findmissingcase.json";

$json = file_get_contents($file);

$data = json_decode($json, true);

$userCounts = [];

foreach ($data as $row) {

    if(isset($row['caseassign_userid'])){

        $userid = $row['caseassign_userid'];

        if(!isset($userCounts[$userid])){
            $userCounts[$userid] = 0;
        }

        $userCounts[$userid]++;

    }

}

foreach ($userCounts as $userid => $count){
    echo $userid . " : " . $count . " cases<br>";
}

?>