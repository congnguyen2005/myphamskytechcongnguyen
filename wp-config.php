<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'myphamcongnguyenskytech' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '=K#!k.1<XY3dOJ]+dwem3rSA21{.~a,)3UjDsFJs{+ceMkUnN.)<]Tqx4itAz93+' );
define( 'SECURE_AUTH_KEY',  '$ NVha cI!!#RV``%vEK#l.reRFM@p74`D+^*nf-9ZiYt:<.j)zl5O)SeF<]=NSd' );
define( 'LOGGED_IN_KEY',    'y(w<Fg55Qp_8pn<+Fgm00.RaRv=;j7 L;tg/6GZG~zy^(FRV$}K%!ep*/8Y$bOD%' );
define( 'NONCE_KEY',        '+[iZ/}UKooH[qw|uG309.sS}]yfr(^3&hO`ndcQvFO(N+mOC;]L,8{f`|>}4iKYh' );
define( 'AUTH_SALT',        '/@k/%C~gJIMW7<=3:_6Ey0j)2,)gY%z>L2}:8;trwe~+6+6HvUZ^GcS@ULg&xJkt' );
define( 'SECURE_AUTH_SALT', 'XT{%XUK853;$OgL:t.VN/hx;ipLdUUHD#/<a^9QaOq7|iwcj<r__s[}h*7W0T.aM' );
define( 'LOGGED_IN_SALT',   'X%H_H`^#~7=:r+W)@f403To7v[fM|W-<W:MX|pZts>n-50B9Z[,3tjGglj;.K1$J' );
define( 'NONCE_SALT',       'zh}*x]$0??OJ5rp.nodw-M.~RSFyWsexG:!Z|>426jI5Z_;rq1OWfA,YB)4RTXdQ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
// define( 'WP_DISABLE_FATAL_ERROR_HANDLER', false ); // Có thể bỏ comment nếu cần
// @ini_set( 'display_errors', 0 );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
