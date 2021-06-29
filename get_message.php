<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // $id_user = $_POST['id_user'];
        // $user_cat = $_POST['user_cat'];
        $id_requete = $_POST['id_requete'];

        // if($user_cat == 'user_app'){
            $sql = "SELECT m.id,m.message,m.user_cat,m.id_requete,m.id_user_app,m.id_conducteur,m.creer,u.nom,u.prenom,c.nom as nomConducteur, c.prenom as prenomConducteur
            FROM tj_message m, tj_user_app u, tj_conducteur c WHERE m.id_user_app=u.id AND m.id_conducteur=c.id AND m.id_requete=$id_requete ORDER BY m.id ASC";
        // }else{
            // $sql = "SELECT m.id,m.message,m.id_requete,m.id_user_app,m.id_conducteur,m.creer,c.nom,c.prenom
            // FROM tj_message m, tj_conducteur c WHERE m.id_conducteur=c.id AND m.id_conducteur=$id_user AND m.id_requete=$id_requete ORDER BY m.id DESC";
        // }
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