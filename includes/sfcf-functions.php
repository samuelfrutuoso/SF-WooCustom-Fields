<?php

// Faz o gancho do hook de ação 'admin_menu', executa a função 'mfp_Add_My_Admin_Link()'
add_action('admin_menu', 'sfcf_Add_My_Admin_Link');
// Adiciona um novo link de topo ao menu do Painel de Controle do Administrador
function sfcf_Add_My_Admin_Link()
{
  add_menu_page(
    'SF WooCustom Fields', // Título da página
    'SF WooCustom Fields', // Texto para exibir no link do menu
    'manage_options', // Requerimento de capacidade para visualizar o link
    'includes/sfcf-page.php', // O 'slug' - arquivo a ser exibido ao clicar no link
    'sfcf_gera_pagina',
  );
}

function sfcf_gera_pagina()
{
}
