<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aw_wordpress_0813');

/** MySQL database username */
define('DB_USER', 'aw_wp_0813');

/** MySQL database password */
define('DB_PASSWORD', 'te5SHZqWXs7tzpTt');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '(1%0zZm+,&KG%8w+R.i~Ayq!lF|KV_iZ?Z]niz$m6+~[zy3+zhGOeLbF6&<,^y 8');
define('SECURE_AUTH_KEY',  'nE |a[D2.pz,5qLQgz-JW>l[z]8(.;]wUtt;HI(gNO-:3||y-F?uy{{ipx0pT{r|');
define('LOGGED_IN_KEY',    'Iw]NAMD?K}*Mwl;@;KSxN+Oy4> !uZ+ `V{-wB6SU[14@k[qsy_P+U/WkBm9K7i^');
define('NONCE_KEY',        '(Cxb$:Z.hnFt|j9qii/`-Gl9S x-9|D!51f`qo/DMuSV_w7BG*q&98{=g[kb[O!*');
define('AUTH_SALT',        'C>a@cKY[Cis5Ep2&5Xts|h~BR8lla--)=0NaVkOU(~GsW{8e3B1 2BUMKP/W4(PX');
define('SECURE_AUTH_SALT', 'W:9([05RAWwJ~:Q9uF`U(vATj9>t7|VK:Q!_Q,PPL6Y|a!-+ETV>y??+|P#;Qj(G');
define('LOGGED_IN_SALT',   'H,%u{1ydAu9*|-2j7tkj; )Bm5/]7fv!G:5|u(E>,oqv%|1DdICnzN-/ie])+Jm}');
define('NONCE_SALT',       ')emWZ~`VMs|^NB~<vE&Ok/eI{t=)8WEg#;sOk ^a$Z2:V;l<BI+DQLl{OY_~`vO^');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'whit_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
