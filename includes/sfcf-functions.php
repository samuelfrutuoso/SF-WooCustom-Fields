<?php

require_once plugin_dir_path(__FILE__) . 'sfcf-page.php';

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


// Custom WooCommerce Checkout Fields based on Quantity
add_action('woocommerce_before_order_notes', 'detalhes_participante');
function detalhes_participante($checkout)
{
  $cart_items = WC()->cart->get_cart();

  // Contador para iterar sobre os itens
  $i = 1;

  // Iterar sobre os itens do carrinho
  foreach ($cart_items as $cart_item_key => $cart_item) {
    // Obtém o nome do produto
    $product_name = $cart_item['data']->get_name();
    $product_quantity = $cart_item['quantity'];

    // Faz iteração para cada item do produto
    for ($j = 1; $j <= $product_quantity; $j++) {
      echo '<h3>Detalhes do participante ' . $i . '</h3>';

      woocommerce_form_field(
        'cstm_produto' . $i,
        array(
          'type'              => 'text',
          'class'             => array('produto_participante form-row-wide'),
          'label'             => __('Produto'),
          'required'          => true,
          'default'           => $product_name,
          'custom_attributes' => array('readonly' => 'readonly')
        ),
        $checkout->get_value('cstm_produto' . $i)
      );

      woocommerce_form_field(
        'cstm_full_name' . $i,
        array(
          'type'          => 'text',
          'class'         => array('nome_participante form-row-wide'),
          'label'         => __('Nome completo'),
          'placeholder'   => __('Insira o nome completo'),
          'required'      => true,
        ),
        $checkout->get_value('cstm_full_name' . $i)
      );

      echo '<div class="clear"></div>';

      woocommerce_form_field(
        'cstm_phone' . $i,
        array(
          'type'          => 'text',
          'class'         => array('telefone_participante form-row-first'),
          'label'         => __('Telefone / WhatsApp'),
          'placeholder'   => __('Insira o telefone / whatsapp'),
          'required'      => true,
        ),
        $checkout->get_value('cstm_phone' . $i)
      );

      woocommerce_form_field(
        'cstm_email' . $i,
        array(
          'type'          => 'email',
          'class'         => array('email_participante form-row-last'),
          'label'         => __('Endereço de email'),
          'placeholder'   => __('Insira o email'),
          'required'      => true,
          'validate'      => 'validate-email',
        ),
        $checkout->get_value('cstm_email' . $i)
      );

      $i++; // Incrementa o contador
    }
  }
}

/**
 * Salva os dados
 */
add_action('woocommerce_checkout_update_order_meta', 'detalhes_participante_update_order_meta');
function detalhes_participante_update_order_meta($order_id)
{
  $quantidade = WC()->cart->get_cart_contents_count();

  for ($i = 2; $i <= $quantidade; $i++) {
    if (!empty($_POST['cstm_produto' . $i])) {
      update_post_meta($order_id, 'Produto ' . $i, sanitize_text_field($_POST['cstm_produto' . $i]));
    }
    if (!empty($_POST['cstm_full_name' . $i])) {
      update_post_meta($order_id, 'Nome completo do participante ' . $i, sanitize_text_field($_POST['cstm_full_name' . $i]));
    }
    if (!empty($_POST['cstm_phone' . $i])) {
      update_post_meta($order_id, 'Telefone do participante ' . $i, sanitize_text_field($_POST['cstm_phone' . $i]));
    }
    if (!empty($_POST['cstm_email' . $i])) {
      update_post_meta($order_id, 'Email do participante ' . $i, sanitize_text_field($_POST['cstm_email' . $i]));
    }
  }
}

/**
 * Adiciona dados na área administrativa
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'detalhes_participante_area_administrativa');
function detalhes_participante_area_administrativa($order)
{

  $quantidade = WC()->cart->get_cart_contents_count();

  for ($i = 1; $i <= $quantidade; $i++) {
    $produto  = $order->get_meta('Produto ' . $i);
    $nome     = $order->get_meta('Nome completo do participante ' . $i);
    $telefone = $order->get_meta('Telefone do participante ' . $i);
    $email    = $order->get_meta('Email do participante ' . $i);

    echo '<h3>Detalhes do participante ' . $i . '</h3>';
    echo '<ul>';
    if (!empty($produto)) {
      echo '<li><strong>Produto ' . $i . ':</strong> ' . $produto . '</li>';
    }
    if (!empty($nome)) {
      echo '<li><strong>Participante ' . $i . ':</strong> ' . $nome . '</li>';
    }
    if (!empty($telefone)) {
      echo '<li><strong>Telefone ' . $i . ':</strong> ' . $telefone . '</li>';
    }
    if (!empty($email)) {
      echo '<li><strong>Email ' . $i . ':</strong> ' . $email . '</li>';
    }
    echo '</ul>';
  }
}

/**
 * Adiciona os dados no email
 **/
// add_filter('woocommerce_email_order_meta_fields', 'detalhes_participante_email_order_meta_fields', 10, 3);
// function detalhes_participante_email_order_meta_fields($fields, $sent_to_admin, $order)
// {
// 	$quantidade = WC()->cart->get_cart_contents_count();

// 	for ($i = 1; $i <= $quantidade; $i++) {
// 		$produto = get_post_meta($order->get_id(), 'Produto ' . $i, true);
// 		$nome = get_post_meta($order->get_id(), 'Nome completo do participante ' . $i, true);
// 		$telefone = get_post_meta($order->get_id(), 'Telefone do participante ' . $i, true);
// 		$email = get_post_meta($order->get_id(), 'Email do participante ' . $i, true);

// 		$fields['Participante ' . $i] = array(
// 			'label' => 'Participante ' . $i,
// 			'value' => "Nome: " . $nome . "\nTelefone: " . $telefone . "\nEmail: " . $email,
// 		);
// 	}

// 	return $fields;
// }

/**
 * Adiciona itens ao recibo
 */
// add_filter('woocommerce_get_order_item_totals', 'detalhes_participante_order_item_totals', 10, 2);
// function detalhes_participante_order_item_totals($total_rows, $order)
// {
// 	$quantidade = WC()->cart->get_cart_contents_count();

// 	for ($i = 1; $i <= $quantidade; $i++) {
// 		$produto = get_post_meta($order->get_id(), 'Produto ' . $i, true);
// 		$nome = get_post_meta($order->get_id(), 'Nome completo do participante ' . $i, true);
// 		$telefone = get_post_meta($order->get_id(), 'Telefone do participante ' . $i, true);
// 		$email = get_post_meta($order->get_id(), 'Email do participante ' . $i, true);

// 		$total_rows['participante_' . $i] = array(
// 			'label' => 'Participante ' . $i . ':',
// 			'value' => "Produto " . $produto . "<br>Nome: " . $nome . "<br>Telefone: " . $telefone . "<br>Email: " . $email,
// 		);
// 	}

// 	return $total_rows;
// }

/**
 * Fim da modificação para múltiplos participantes
 */
