<?php
/**
 * Template Name: Workshops Page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

$args = array(
  'post_type' => 'workshop',
);
$context['workshops'] = Timber::get_posts($args);
$context['registration_page'] = Timber::get_post($context['options']['workshop_registration_page']);

$templates = array( 'page-workshops.twig', 'index.twig' );
Timber::render( $templates, $context );
