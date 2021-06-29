<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    $months = array ("January"=>'Jan',"February"=>'Fev',"March"=>'Mar',"April"=>'Avr',"May"=>'Mai',"June"=>'Jun',"July"=>'Jul',"August"=>'Aou',"September"=>'Sep',"October"=>'Oct',"November"=>'Nov',"December"=>'Déc');
    
    // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_conducteur = $_POST['id_conducteur'];

        $sql = "SELECT id FROM tj_conducteur";
        $result = mysqli_query($con,$sql);
        $id_conducteur = "0";
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $id_conducteur = $id_conducteur.','.$row['id'];
        }

        // echo $id_conducteur;

        $sql = "SELECT r.id,r.id_user_app,r.latitude_depart,r.longitude_depart,r.latitude_arrivee,r.longitude_arrivee,
        r.statut,r.id_conducteur,r.id_user_app,r.creer,u.nom,u.prenom,r.distance,r.statut_course,r.montant,r.duree
        FROM tj_requete r, tj_user_app u
        WHERE r.id_user_app=u.id AND r.id_conducteur IN ($id_conducteur) AND r.statut='en cours'
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