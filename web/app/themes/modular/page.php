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

$templates = array( 'page.twig', 'index.twig' );
Timber::render( $templates, $context );
