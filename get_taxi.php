<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    
    // if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $sql = "SELECT t.id,t.numero,t.immatriculation,t.statut,c.latitude,c.longitude,t.creer,t.modifier,
        tv.libelle as libTypeVehicule
        FROM tj_taxi t, tj_type_vehicule tv, tj_affectation a, tj_conducteur c
        WHERE t.id_type_vehicule=tv.id AND a.id_taxi=t.id AND a.id_conducteur=c.id AND t.statut='yes' AND c.online='yes'";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $output[] = $row;
        }
        
        if(mysqli_num_rows($result) > 0){
            $response['msg'] = $output;
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>