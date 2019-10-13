<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_thumbnails
 *
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */

// no direct access
defined('_JEXEC') or die;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

class modarticlesthumbnailsHelper
{
	/**
	 * Get the articles to show
	 *
	 * @param	object	$params	Module parameters
	 *
	 * @return	array	Array of items
	 */
	static public function getItems($params)
	{
		// Set application parameters in model
		$app       = JFactory::getApplication();
		$appParams = $app->getParams();

		$option = $app->input->get('option');
		$view   = $app->input->get('view');

		$onlyInArticles = $params->get('show_only_in_articles');

		$relatedArticles = true;

		if ($onlyInArticles)
		{
			$option = $app->input->get('option');
			$view   = $app->input->get('view');

			if (!($option === 'com_content' && $view === 'article'))
			{
				return array();
			}

			$temp = $app->input->getString('id');
			$temp = explode(':', $temp);
			$id   = $temp[0];
		}

		// Get an instance of the generic articles model
		$model     = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		switch ($params->get('related_mode', 'false'))
		{
			case 'category':
				$categoryId = self::getArticleCategory($id);
				$model->setState('filter.category_id', $categoryId);
				break;
		}

		// Set application parameters in model
		$app       = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 3));
		$model->setState('filter.published', 1);
		$model->setState('filter.featured', $params->get('show_front', 1) == 1 ? 'show' : 'hide');

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		if (!isset($categoryId))
		{
			$model->setState('filter.category_id', $params->get('catid', array()));
		}

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Ordering
		if ($params->get('ordering') == 'random')
		{
			$model->setState('list.ordering', JFactory::getDbo()->getQuery(true)->Rand());
		}
		else
		{
			$model->setState('list.ordering', 'a.' . $params->get('ordering', 'publish_up'));
			$model->setState('list.direction', $params->get('direction', 'DESC'));
		}

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}

	/**
	 * Get current Article Category
	 *
	 * @param	integer	$id	Article id
	 *
	 * @return	integer	Category id
	 */
	public static function getArticleCategory($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Select the meta keywords from the item
		$query->select('catid')
			->from('#__content')
			->where('id = ' . (int) $id);
		$db->setQuery($query);

		try
		{
			$catId = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('MOD_ARTICLE_THUMBNAILS_AN_ERROR_HAS_OCCURRED'), 'error');

			$catId = 0;
		}

		return $catId;

	}

}
