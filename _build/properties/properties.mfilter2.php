<?php
/**
 * Properties for the mSearch2 snippet.
 *
 * @package msearch2
 * @subpackage build
 */

$properties = array();

$tmp = array(
	'paginator' => array(
		'type' => 'textfield'
		,'value' => 'pdoPage'
	),
	'element' => array(
		'type' => 'textfield'
		,'value' => 'mSearch2'
	),

	'sort' => array(
		'type' => 'textfield'
		,'value' => ''
	),
	'filters' => array(
		'type' => 'textarea'
		,'value' => 'resource|parent:parents'
	),
	'aliases' => array(
		'type' => 'textarea'
		,'value' => ''
	),
	'showEmptyFilters' => array(
		'type' => 'combo-boolean'
		,'value' => true
	),

	'resources' => array(
		'type' => 'textfield'
		,'value' => ''
	),
	'parents' => array(
		'type' => 'textfield'
		,'value' => ''
	),
	'depth' => array(
		'type' => 'numberfield'
		,'value' => 10
	),

	'tplOuter' => array(
		'type' => 'textfield'
		,'value' => 'tpl.mFilter2.outer'
	),
	'tplFilter.outer.default' => array(
		'type' => 'textfield'
		,'value' => 'tpl.mFilter2.filter.outer'
	),
	'tplFilter.row.default' => array(
		'type' => 'textfield'
		,'value' => 'tpl.mFilter2.filter.checkbox'
	),

	'showHidden' => array(
		'type' => 'combo-boolean'
		,'value' => true
	),
	'showDeleted' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),
	'showUnpublished' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),
	'hideContainers' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),

	'showLog' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),
	'fastMode' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),
	'suggestions' => array(
		'type' => 'combo-boolean'
		,'value' => true
	),
	'suggestionsMaxFilters' => array(
		'type' => 'numberfield'
		,'value' => 200
	),
	'suggestionsMaxResults' => array(
		'type' => 'numberfield'
		,'value' => 1000
	),
	'suggestionsRadio' => array(
		'type' => 'textfield'
		,'value' => ''
	),

	'toPlaceholders' => array(
		'type' => 'textfield'
		,'value' => ''
	),
	'toSeparatePlaceholders' => array(
		'type' => 'textfield'
		,'value' => ''
	),

	'filter_delimeter' => array(
		'type' => 'textfield'
		,'value' => '|'
	),
	'method_delimeter' => array(
		'type' => 'textfield'
		,'value' => ':'
	),
	'values_delimeter' => array(
		'type' => 'textfield'
		,'value' => ','
	),
	'tpls' => array(
		'type' => 'textfield'
		,'value' => ''
	),

	'forceSearch' => array(
		'type' => 'combo-boolean'
		,'value' => false
	),
	'fields' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'onlyIndex' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k,
			'desc' => 'mse2_prop_'.$k,
			'lexicon' => 'msearch2:properties',
		), $v
	);
}

return $properties;