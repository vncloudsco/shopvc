<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'myphamohuii');

/** MySQL database username */
define('DB_USER', 'kidz');

/** MySQL database password */
define('DB_PASSWORD', '37,kW]x<-hV+G>.%');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ':+znT4u0IHQ4G|Jhl1.zm@Y-C%I=HcXG:$mdds+)ef%H=%TKbTV6wQV>Q^K-{_^?');
define('SECURE_AUTH_KEY',  'N^FRc]16:Reh:}XhdGn*3RgMi}ccF%12_#$AMRm(!{h(^V[-RBHnyvOlzm]|IolR');
define('LOGGED_IN_KEY',    '~);vh/$Wia,y7`)W~>!#XFHC(##{km+7p>G>~[:]8zfigF8V#g4~`5nrex#iyG-s');
define('NONCE_KEY',        'VP392XAM0KCZ_Z}R_WWkPLC<cwl6Qj|/$g)=OTBXG7mLKHaVZ}M0j^Cqc!}]Pa|n');
define('AUTH_SALT',        'M)ZR8HRs4>,>sT<Gd#W^fyBgYE^Q.eN]{dyTba{6;Kh2!7WnPl*kHxQh:XXf+M#q');
define('SECURE_AUTH_SALT', ')2 %z9rfJ+dQYO#>4lfuFcOb3d@!2QKYtA;!8y_,?PK*^qMjt sk{/*./F;iu_H9');
define('LOGGED_IN_SALT',   'r~n~Ds6J/=l*QaC}[%?.t uV1Czvs@9!]m_SSmcuv^ob3L1jceta0}vH(;Yt/Tvb');
define('NONCE_SALT',       '|qNvw$(17Wn2Xs6;;(#mjXaG}-!*5UjOS{#O<t5(q&{JTfm/q<02D5&mI9,%J[qk');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
