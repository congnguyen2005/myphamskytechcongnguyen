<?php
/**
 * Template Name: Liên hệ (Removed)
 * @package MyPhamTheme
 *
 * This template intentionally returns a 410 Gone and redirects to home.
 */

// Send 410 Gone header and redirect
status_header(410);
wp_safe_redirect(home_url(), 302);
exit;