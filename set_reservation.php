<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	// include("query/fonction.php");
  $con->set_charset("utf8");
  include_once 'GCM.php';
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $user_id = $_POST['user_id'];
        $lat1 = $_POST['lat1'];
        $lng1 = $_POST['lng1'];
        $lat2 = $_POST['lat2'];
        $lng2 = $_POST['lng2'];
        $cout = $_POST['cout'];
        $duree = $_POST['duree'];
        $distance = $_POST['distance'];
        $date_depart = $_POST['date_depart'];
        $heure_depart = $_POST['heure_depart'];
        $contact = $_POST['contact'];
        $date_heure = date('Y-m-d H:i:s');

        $query = "INSERT INTO tj_reservation_taxi(id_user_app,latitude_depart,longitude_depart,latitude_arrivee,longitude_arrivee
        ,cout,distance,date_depart,heure_depart,statut,creer,contact,duree)
        VALUES($user_id,'$lat1','$lng1','$lat2','$lng2',$cout,'$distance','$date_depart','$heure_depart','en cours','$date_heure','$contact','$duree')";
        mysqli_query($con, $query);
        
        $tmsg='';
        $terrormsg='';
        
        $title=str_replace("'","\'","Taxi booking");
        $msg=str_replace("'","\'","A customer has just sent a taxi reservation request");
        
        $tab[] = array();
        $tab = explode("\\",$msg);
        $msg_ = "";
        for($i=0; $i<count($tab); $i++){
            $msg_ = $msg_."".$tab[$i];
        }

        $gcm = new GCM();

        $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>"reservation");

        $query = "select fcm_id from tj_conducteur where fcm_id<>''";
        $result = mysqli_query($con, $query);

        $tokens = array();
        if (mysqli_num_rows($result) > 0) {
            while ($user = $result->fetch_assoc()) {
                if (!empty($user['fcm_id'])) {
                    $tokens[] = $user['fcm_id'];
                }
            }
        }
        $temp = array();
        if (count($tokens) > 0) {
            $gcm->send_notification($tokens, $message, $temp);
        }

        $response['msg']['etat'] = 1;
        echo json_encode($response);
        mysqli_close($con);
    // }
?>