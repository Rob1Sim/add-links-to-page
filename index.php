<?php
/*
Plugin Name: Add pages for the extranet
Plugin URI: https://github.com/Rob1Sim/more-userdata-for-istep
Description: Gère les pages qui sont disponible dans l'extranet
Author: Robin Simonneau
Version: 1.0
Author URI: https://robin-sim.fr/
*/
wp_enqueue_script('add-pages-extranet-admin', plugins_url('./scripts/add-pages-extranet-admin.js', __FILE__), array(), false, true);
/**
 * Appeler lors de l'activation du plugin
 * @return void
 */
function on_enabled(): void {
    $page_data = array(
        'post_title' => "Link to extranet",
        'post_content' => "",
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author'=>1,
        'post_name' => "link-extranet",
    );
    #Création de la page qui contient les liens
    wp_insert_post($page_data);

    #Stockage des pages qui sont présente sur l'extranet
    update_option("link-extranet",[]);

}

/**
 * Appler lorsque le plugin est désactivé
 * @return void
 */
function on_disabled(): void {
    $page_id = get_page_by_path("link-extranet");
    wp_delete_post($page_id->ID, true);
    update_option("link-extranet",[]);
}

register_activation_hook(__FILE__, 'on_enabled');
register_deactivation_hook(__FILE__, 'on_disabled');



/**
 * Menu dans le panel d'administration
 * @return void
 */
function add_pages_extranet_menu(): void
{
    add_menu_page(
        "Pages disponibles sur l'extranet",
        "Pages Extranet",
        'manage_options',
        "add-pages-extranet-options",
        "list_all_pages"
    );
}

add_action('admin_menu', 'add_pages_extranet_menu');

/**
 * Pages qui gère l'ajout des pages vers l'extranet
 * @return void
 */
function list_all_pages():void{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    //Traitement du formulaire
    if (isset($_POST["extranet-apge-sbmt"]) && !isset($_POST["extranet_pages"])){
        update_option("link-extranet",[]);
    }

    if (isset($_POST["extranet-apge-sbmt"]) && isset($_POST["extranet_pages"])){

        $pages_id = $_POST["extranet_pages"];
        $extranet_page = get_page_by_path("link-extranet");
        $page_content = "";
        $links = get_option("link-extranet");

        //Enregistre les id dans la liste des liens
        update_option("link-extranet",$pages_id);
        //Ajoute tout les nouveaux liens
        foreach ($pages_id as $link){
            $url = get_permalink($link);
            $page_content .="<a href='".$url."'>lien</a>";
        }

        //Met à jour la page des liens
        $extranet_page->post_content = $page_content;
        wp_update_post($extranet_page);
    }

    //Affichage du formulaire
    $pages = get_pages();
    echo '<h1>Liste des pages</h1>';
    if ($pages) {
        $links_already_save = get_option("link-extranet");
        echo '<label for="search-page">Chercher : </label>';
        echo '<input type="text" id="search-page"/>';
        echo '<form action="" method="post">';
        echo '<table class="wp-list-table widefat striped" id="pages-list">';
        echo '<thead><tr><th scope="col">Nom de la page</th><th scope="col">Disponible sur l\'extranet</th></tr></thead>';
        echo '<tbody>';
        $extranet_page = get_page_by_path("link-extranet");
        foreach ($pages as $final_url) {
            if ($final_url->ID != $extranet_page->ID){
                echo '<tr>';
                echo '<td class="title column-title"><strong><a href="' . get_permalink($final_url->ID) . '">' . $final_url->post_title.'_'.$final_url->ID . '</a></strong></td>';
                echo '<td class="check-column">
                            <input type="checkbox" name="extranet_pages[]"
                            value="' . $final_url->ID. '" '.(in_array($final_url->ID, $links_already_save)?"checked":"").'>
                      </td>';
                echo '<input name="link[]" type="hidden" value="'.get_permalink($final_url->ID).'"/>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';
        echo '<button type="submit" class="button" name="extranet-apge-sbmt">Mettre à jour</button>';
        echo '</form>';
    } else {
        echo 'Aucune page trouvée.';
    }
}