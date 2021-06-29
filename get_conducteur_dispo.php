<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $lat1 = $_POST['lat1'];
        $lng1 = $_POST['lng1'];

        $sql = "SELECT t.id,t.statut,c.latitude,c.longitude,c.id as idConducteur,c.nom,c.prenom,t.immatriculation,t.numero
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

        if(mysqli_num_rows($result) > 0){
            $response['msg'] = $output;
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 0;
        }
        echo json_encode($response);
        mysqli_close($con);
    // }
?>