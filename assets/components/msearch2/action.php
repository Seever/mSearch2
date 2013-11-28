<?php

if (!isset($modx)) {
	define('MODX_API_MODE', true);
	require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';
	$modx->getService('error','error.modError');
	$modx->getRequest();
	$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
	$modx->setLogTarget('FILE');
	$modx->error->message = null;
}

if (empty($_REQUEST['action'])) {
	exit($modx->toJSON(array('success' => false, 'message' => 'Access denied')));
}
else {
	$action = $_REQUEST['action'];
}

if (!empty($_REQUEST['pageId']) && !$modx->resource) {
	$modx->resource = $modx->getObject('modResource', $_REQUEST['pageId']);
	$config = $_SESSION['mFilter2'][@$_REQUEST['pageId']]['scriptProperties'];
	if ($modx->resource->get('context_key') != 'web') {
		$modx->switchContext($modx->resource->context_key);
	}
}
else {$config = array();}

/* @var mSearch2 $mSearch2 */
$mSearch2 = $modx->getService('msearch2','mSearch2', MODX_CORE_PATH.'components/msearch2/model/msearch2/', $config);
$mSearch2->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/', $config);
$pdoFetch->addTime('pdoTools loaded.');

switch ($action) {
	case 'filter':
		$paginatorProperties = $_SESSION['mFilter2'][@$_REQUEST['pageId']]['paginatorProperties'];
		unset($_REQUEST['pageId'], $_REQUEST['action']);

		// Get sorting parameters
		if (!empty($_REQUEST['sort'])) {
			$sort = $_REQUEST['sort'];
		}
		else if (!empty($paginatorProperties['defaultSort'])) {
			$sort = $paginatorProperties['defaultSort'];
		}
		$paginatorProperties['sortby'] = !empty($sort)
			? $mSearch2->getSortFields($sort)
			: '';
		$paginatorProperties['sortdir'] = '';

		if (empty($_REQUEST['limit'])) {
			$paginatorProperties['limit'] = $_REQUEST['limit'] = $paginatorProperties['start_limit'];
		}

		// Switching chunk for rows, if specified
		if (!empty($paginatorProperties['tpls']) && is_array($paginatorProperties['tpls'])) {
			$tmp = isset($_REQUEST['tpl']) ? (integer) $_REQUEST['tpl'] : 0;
			if (isset($paginatorProperties['tpls'][$tmp])) {
				$paginatorProperties['tpl'] = $paginatorProperties['tpls'][$tmp];
			}
		}

		// Processing filters
		if (strpos($paginatorProperties['resources'], '{') === 0) {
			$found = $modx->fromJSON($paginatorProperties['resources']);
			$ids = array_keys($found);
		}
		else {
			$ids = explode(',', $paginatorProperties['resources']);
		}

		$resources = implode(',', $ids);
		$pdoFetch->addTime('Getting filters for saved ids: ('.$resources.')');

		$matched = $mSearch2->Filter($ids, $_REQUEST);
		$ids = array_intersect($ids, $matched);

		$pdoFetch->addTime('Filters retrieved.');
		if (!empty($config['suggestions'])) {
			$suggestions = $mSearch2->getSuggestions($resources, $_REQUEST, $ids);
			$pdoFetch->addTime('Suggestions retrieved.');
		} else {
			$suggestions = array();
			$pdoFetch->addTime('Suggestions disabled by snippet parameter.');
		}

		// Saving log
		$log = $pdoFetch->timings;
		$pdoFetch->timings = array();

		// Retrieving results
		if (!empty($ids)) {
			$_GET = $_REQUEST;

			$paginatorProperties['resources'] = is_array($ids) ? implode(',', $ids) : $ids;
			// Saving search sort
			if (empty($paginatorProperties['sortby'])) {
				$paginatorProperties['sortby'] = "find_in_set(`".$pdoFetch->config['class']."`.`id`,'".$paginatorProperties['resources']."')";
			}
			// Trying to save weight of found ids if using mSearch2
			if (!empty($found) && strtolower($paginatorProperties['element']) == 'msearch2') {
				$tmp = array();
				foreach ($ids as $v) {
					$tmp[$v] = @$found[$v];
				}
				$paginatorProperties['resources'] = $modx->toJSON($tmp);
			}

			$results = $modx->runSnippet($mSearch2->config['paginator'], $paginatorProperties);
			$pagination = $modx->getPlaceholder($paginatorProperties['pageNavVar']);
			$total = $modx->getPlaceholder($paginatorProperties['totalVar']);

			if (!empty($paginatorProperties['fastMode'])) {
				$results = $pdoFetch->fastProcess($results);
				$pagination = $pdoFetch->fastProcess($pagination);
			}
			else {
				$maxIterations= (integer) $modx->getOption('parser_max_iterations', null, 10);
				$modx->getParser()->processElementTags('', $results, false, false, '[[', ']]', array(), $maxIterations);
				$modx->getParser()->processElementTags('', $results, true, true, '[[', ']]', array(), $maxIterations);
				$modx->getParser()->processElementTags('', $pagination, false, false, '[[', ']]', array(), $maxIterations);
				$modx->getParser()->processElementTags('', $pagination, true, true, '[[', ']]', array(), $maxIterations);
			}
		}
		else {
			$results = $pagination = '';
		}

		$pdoFetch->timings = $log;
		$pdoFetch->addTime('Total filter operations: '.$mSearch2->filter_operations);
		$response = array(
			'success' => true
			,'message' => ''
			,'data' => array(
				'results' => !empty($results) ? $results : $modx->lexicon('mse2_err_no_results')
				,'pagination' => $pagination
				,'total' => empty($total) ? 0 : $total
				,'suggestions' => $suggestions
				,'log' => ($modx->user->hasSessionContext('mgr') && !empty($config['showLog'])) ? print_r($pdoFetch->getTime(), 1) : ''
			)
		);
		$response = $modx->toJSON($response);
	break;

	default:
		$response = $modx->toJSON(array('success' => false, 'message' => 'Access denied'));
}

@session_write_close();
exit($response);