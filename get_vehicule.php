<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    
    // if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $sql = "SELECT v.id,v.nombre,v.statut,v.prix,v.nb_place,v.creer,v.modifier,v.image,
        tv.libelle as libTypeVehicule
        FROM tj_vehicule v, tj_type_vehicule tv
        WHERE v.id_type_vehicule=tv.id";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $id_vehicule = $row['id'];
            $sql_nb = "SELECT count(id) as nb FROM tj_location_vehicule WHERE id_vehicule=$id_vehicule AND statut='accepter'";
            $result_nb = mysqli_query($con,$sql_nb);
            $nb = 0;
            while($row_nb = mysqli_fetch_assoc($result_nb)) {
                $nb = $row_nb['nb'];
            }
            $row['nb_reserve'] = $nb;
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