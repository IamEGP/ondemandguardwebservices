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
        $date_heure = date('Y-m-d H:i:s');

        $sql = "SELECT t.id,t.statut,c.latitude,c.longitude,c.id as idConducteur
        FROM tj_taxi t, tj_type_vehicule tv, tj_affectation a, tj_conducteur c
        WHERE t.id_type_vehicule=tv.id AND a.id_taxi=t.id AND a.id_conducteur=c.id AND t.statut='yes' AND c.online!='no'";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            if($row['latitude'] != '' && $row['latitude'] != '')
                $row['distance'] = distance($row['latitude'],$row['longitude'],$lat1,$lng1,'K');
            $output[] = $row;
        }

        function cmp($a,$b){
            return strcmp($a["distance"], $b["distance"]);
        }
        usort($output, "cmp");

        // print_r($output);
        // print_r($output);
        // echo $output[0]['distance'];
        $j=0;
        $input_id = "0";
        while($j<5 && $j<count($output)){
          $id_conducteur = $output[$j]['idConducteur'];
          if($j == 0)
            $input_id = $id_conducteur;
          else
            $input_id = $input_id.",".$id_conducteur;
          
          $tmsg='';
          $terrormsg='';
          
          $title=str_replace("'","\'","Race request");
          $msg=str_replace("'","\'","You have just received a request from a client");
          
          $tab[] = array();
          $tab = explode("\\",$msg);
          $msg_ = "";
          for($i=0; $i<count($tab); $i++){
              $msg_ = $msg_."".$tab[$i];
          }

          $gcm = new GCM();

          $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>"requete");

          $query = "select fcm_id from tj_conducteur where fcm_id<>'' and id=$id_conducteur";
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

          $j++;
        }

        function distance($lat1, $lon1, $lat2, $lon2, $unit) {
          if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
        
            if ($unit == "K") {
              return ($miles * 1.609344);
            } else if ($unit == "N") {
              return ($miles * 0.8684);
            } else {
              return $miles;
            }
          }
        }

        $date_heure = date('Y-m-d H:i:s');
        $query = "INSERT INTO tj_requete(id_user_app,latitude_depart,longitude_depart,latitude_arrivee,longitude_arrivee,statut,id_conducteur,creer,distance,montant,duree)
        VALUES($user_id,'$lat1','$lng1','$lat2','$lng2','en cours','$input_id','$date_heure',$distance,$cout,'$duree')";
        mysqli_query($con, $query);

        $response['msg']['etat'] = 1;
        echo json_encode($response);
        mysqli_close($con);
    // }
?>