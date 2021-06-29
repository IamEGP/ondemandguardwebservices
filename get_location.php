<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_user_app = $_POST['id_user_app'];
        $sql = "SELECT l.id,l.nb_jour,l.date_debut,l.date_fin,l.contact,l.id_vehicule,l.statut,v.prix,v.nb_place,l.creer,l.modifier,v.image,
        tv.libelle as libTypeVehicule
        FROM tj_location_vehicule l, tj_vehicule v, tj_type_vehicule tv
        WHERE l.id_vehicule=v.id AND v.id_type_vehicule=tv.id AND l.id_user_app=$id_user_app ORDER BY l.id DESC";
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
    }
?>