<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'S_F& 70J*OS3>;u$|xh,gY%fDjqNJ; +dI4*jkeG)eUA$W@5=.U#o*HJF;fV i-U' );
define( 'SECURE_AUTH_KEY',   'qr#U.@Z<1+)]_Z2m/ea0O`KFR0?,_X||U;a1<}?U4o8 ]1Q8S=EaWU,LLD%-L90w' );
define( 'LOGGED_IN_KEY',     'u!@#zb6mWmIo[aHStQc9Xc^Ry1uq~`5l7t3T!.vhHE(p!-|Ohxx8T]x`*y+HVd`E' );
define( 'NONCE_KEY',         '5o*)M~p;H[^rV;:geE3#`[[F%5!`.kZDu]J!@)Gk%>$!GtlRU6THwQ>K_=P!5D{s' );
define( 'AUTH_SALT',         'k#tuZg387bt=C,_@&=QY.:s?6R_Ewraw!8^ndYTMe)M*F#ow-E=5dv6Xilm*!CH>' );
define( 'SECURE_AUTH_SALT',  'KPN_VCH:4.or%Y}{6t>XyM!hHdTMplsZ:(J7(B51cPQx#`_a>RSzP6Y(=Dx4wX_M' );
define( 'LOGGED_IN_SALT',    '}!<$#c&&<QYlCoWWFyUL#fB>m3ZxP}hHKtYXVIc@Q&@XC3CbitghX_eb3<utk0s{' );
define( 'NONCE_SALT',        '1tE8#I^N0<AMpTk[C7E:!UfMeNKX[UsT:+s7V}R=QVN$kwnQbc)<S7$Cny.{i05h' );
define( 'WP_CACHE_KEY_SALT', ':4+-PM73=)!qX9oz30um/=5M)(e^p-$SP|9]0/}zN`kB*1JwLc63IJ@-dwcFjW{(' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
