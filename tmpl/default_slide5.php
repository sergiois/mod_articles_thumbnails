<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_thumbnails
 *
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$client = Factory::getApplication()->client;

$slideactive = (int)$params->get('slide_active', 1);
if($client->mobile)
{
    $itemstoshow = 1;
}
else
{
    $itemstoshow = (int)$params->get('count');
}
$itemstotal = count($items);
$slidestoshow = ceil(count($items) / $itemstoshow);
$show=0;

$spanmd = 4;
switch($params->get('count'))
{
    case 1: $spanmd = 12; break;
    case 2: $spanmd = 6; break;
    case 3: $spanmd = 4; break;
    case 4: $spanmd = 3; break;
    default: $spanmd = 4;
}
if($client->mobile)
{
    $spanmd = 12;
}

HTMLHelper::_('bootstrap.carousel', '.selector');
?>

<div id="<?php echo $module->module.$module->id; ?>" class="carousel slide <?php echo $moduleclass_sfx; ?>">
    <div class="carousel-inner">
        <?php for($s=1; $s<=$slidestoshow; $s++){ ?>
        <?php
        $active = '';
        if($s == $slideactive)
        {
            $active = 'active';
        }
        ?>
        <div class="carousel-item <?php echo $active; ?>">
            <div class="row justify-content-md-center">
                <?php for($i=0; $i<$itemstoshow; $i++, $show++){ ?>
                <?php if($items[$show]){ ?>
                <div class="col-sm-<?php echo $spanmd; ?>">
                    <div class="card">
                        <?php if($params->get('show_image') != 'off'): ?>
                            <?php
                            $images = json_decode($items[$show]->images);
                            $image = $images->image_intro;
                            $alt = $images->image_intro_alt ? $images->image_intro_alt : $items[$show]->title;
                            if($params->get('show_image') == 'fulltext')
                            {
                                $image = $images->image_fulltext;
                                $alt = $images->image_fulltext_alt ? $images->image_fulltext_alt : $items[$show]->title;
                            }
                            ?>
                            <?php if($params->get('link_image')): ?>
                                <a href="<?php echo $items[$show]->link; ?>" itemprop="url" target="<?php echo $params->get('open_link', '_self'); ?>">
                                    <img data-src="<?php echo $image; ?>" src="<?php echo $image; ?>" alt="<?php echo $alt; ?>">
                                </a>
                            <?php else: ?>
                                <img data-src="<?php echo $image; ?>" class="card-img-top" src="<?php echo $image; ?>" alt="<?php echo $alt; ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="card-body">
                            <?php if($params->get('show_title')): ?>
                            <h2 class="card-title">
                            <?php if($params->get('link_title')): ?>
                                <a href="<?php echo $items[$show]->link; ?>" itemprop="url" target="<?php echo $params->get('open_link', '_self'); ?>">
                            <?php endif; ?>
                            <?php echo $items[$show]->title; ?>
                            <?php if($params->get('link_title')): ?>
                                </a>
                            <?php endif; ?>
                            </h2>
                            <?php endif; ?>

                            <?php if($params->get('show_category')): ?>
                                <small class="<?php echo $params->get('design_category'); ?>">
                                    <?php echo $items[$show]->category_title; ?>
                                </small>
                            <?php endif; ?>

                            <?php if($params->get('show_date')): ?>
                                <small class="<?php echo $params->get('design_date'); ?>">
                                <?php
                                    $dateformat = 'DATE_FORMAT_LC';
                                    switch((int)$params->get('date_format')){
                                        case 0: $dateformat = 'DATE_FORMAT_LC'; break;
                                        case 1: $dateformat = 'DATE_FORMAT_LC1'; break;
                                        case 2: $dateformat = 'DATE_FORMAT_LC2'; break;
                                        case 3: $dateformat = 'DATE_FORMAT_LC3'; break;
                                        case 4: $dateformat = 'DATE_FORMAT_LC4'; break;
                                        case 5: $dateformat = 'DATE_FORMAT_LC5'; break;
                                        case 6: $dateformat = $params->get('date_custom_format'); break;
                                        default: $dateformat = 'DATE_FORMAT_LC';
                                    }
                                    echo HTMLHelper::_('date', $items[$show]->publish_up, Text::_($dateformat));
                                ?>
                                </small>
                            <?php endif; ?>

                            <?php if($params->get('show_content') != 'offc'): ?>
                                <?php
                                if($params->get('show_content') == 'partc'):
                                    $cleanText = filter_var($items[$show]->introtext, FILTER_SANITIZE_STRING);
                                    $introCleanText = strip_tags($cleanText);
                                    if (strlen($introCleanText) > $params->get('tam_content', 200))
                                    {
                                        $introtext = substr($introCleanText,0,strrpos(substr($introCleanText,0,$params->get('tam_content', 200))," ")).'...';
                                    }
                                    else
                                    {
                                        $introtext = $introCleanText;
                                    }
                                    echo '<p class="card-text">'.$introtext.'</p>';
                                else:
                                    echo '<div class="card-text">'.$items[$show]->introtext.'</div>';
                                endif;
                                ?>
                            <?php endif; ?>
                        </div>
                        <?php if($params->get('show_readmore')): ?>
                        <div class="card-footer">
                            <div class="text-center"><a class="btn btn-secondary btn-block" href="<?php echo $items[$show]->link; ?>"><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_THUMBNAILS_FIELD_READMORE_TEXT'); ?></a></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mt-4">
                <button class="btn btn-outline-secondary mx-1" type="button" data-bs-target="#<?php echo $module->module.$module->id; ?>" data-bs-slide="prev">
                    <i class="fa fa-lg fa-chevron-left"></i>
                    <span class="visually-hidden"><?php echo Text::_('JPREV'); ?></span>
                </button>
                <button class="btn btn-outline-secondary mx-1" type="button" data-bs-target="#<?php echo $module->module.$module->id; ?>" data-bs-slide="next">
                    <i class="fa fa-lg fa-chevron-right"></i>
                    <span class="visually-hidden"><?php echo Text::_('JNEXT'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>