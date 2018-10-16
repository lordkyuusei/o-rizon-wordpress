<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'o-rizon');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'admin');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'SRu13bwaMWrq');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         't&(d75q.:e=oU2-:ZNAYY*]TH@==D]AEvmOlp/W|_]E%XcBQI umH^zfu^z8uWY3');
define('SECURE_AUTH_KEY',  '(/IUd6%BmlfMB!mnQ6wp$$j^w)V8QKXfR:{+@;* wY#I>]u=9Pfc$;JxrSX7uuCC');
define('LOGGED_IN_KEY',    'N16#moM<7nV[HTZ5QU; )xy5TnR~8~|FD87^d{pK-{GhGFkDhWXkeKY[D~iA4^9t');
define('NONCE_KEY',        '8CK7m-QaMJDTp;0PTf:LUE;bdF]TQ/]Q00b$CE]G$0637qum-!D0:-pQP>]`NO%g');
define('AUTH_SALT',        '$>=>Q[xM(eT3iIx#oLp@|oZ%<Ol+:Kx=k{kqVQnhU%pKdsA7S8|-;;<wFb<=7VHb');
define('SECURE_AUTH_SALT', 'U~ ]$BtmX?j0-vp%@-p, NrZCMMDz5c#q-}O<7HDtsJ]j_T^fhytH-gh[d{[/p.<');
define('LOGGED_IN_SALT',   '9B}}@%n-y6ePX+tn-*rDSbl~9H]VXXB- euWkY^^Jl9z2vQ~xm3hWTU+,DHTA-1>');
define('NONCE_SALT',       'p,tE:0`:~Ue5m#wi-hFe:H$l6Kund+m8wd:57mu#yo/{}:05}=s2r!2l.+~4tXlC');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');