<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    $months = array ("January"=>'Jan',"February"=>'Fev',"March"=>'Mar',"April"=>'Avr',"May"=>'Mai',"June"=>'Jun',"July"=>'Jul',"August"=>'Aou',"September"=>'Sep',"October"=>'Oct',"November"=>'Nov',"December"=>'Déc');
    
    // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_user_app = $_POST['id_user_app'];

        $sql = "SELECT r.id,r.id_user_app,r.latitude_depart,r.longitude_depart,r.latitude_arrivee,r.longitude_arrivee,
        r.cout,r.distance,r.date_depart,r.heure_depart,r.statut,r.contact,r.creer,u.nom,u.prenom
        FROM tj_reservation_taxi r, tj_user_app u
        WHERE r.id_user_app=u.id AND r.id_user_app=$id_user_app
        ORDER BY r.id DESC";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $row['creer'] = date("d", strtotime($row['creer']))." ".$months[date("F", strtotime($row['creer']))].". ".date("Y", strtotime($row['creer']));

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