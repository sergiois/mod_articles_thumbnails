<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_thumbnails
 *
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */
defined('_JEXEC') or die;

JLoader::register('modarticlesthumbnailsHelper', __DIR__ . '/helper.php');

$items = modarticlesthumbnailsHelper::getItems($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$layout = $params->get('layout', 'default');
switch((int)$params->get('templateframework', 1))
{
    case 2: $layout .= '_bootstrap3'; break;
    case 3: $layout .= '_uikit'; break;
    case 4: $layout .= '_uikit3'; break;
}

require JModuleHelper::getLayoutPath('mod_articles_thumbnails', $layout);