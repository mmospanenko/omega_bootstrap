<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 * 
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

/**
 * Implements hook_process().
 */
function omega_bootstrap_process(&$vars, $hook) {
  if (!empty($vars['elements']['#grid']) || !empty($vars['elements']['#data']['wrapper_css'])) {
    if (!empty($vars['elements']['#grid'])) {
      foreach (array('prefix', 'suffix', 'push', 'pull') as $quality) {
        if (!empty($vars['elements']['#grid'][$quality])) {
          array_unshift($vars['attributes_array']['class'], 'offset' . $vars['elements']['#grid'][$quality]); # Добавляем класс offset* региону
        }
      }

      array_unshift($vars['attributes_array']['class'], 'span' . $vars['elements']['#grid']['columns']); # Добавляем класс span* региону
    }
  
    $vars['attributes'] = $vars['attributes_array'] ? drupal_attributes($vars['attributes_array']) : '';
  }

  if (!empty($vars['elements']['#grid_container']) || !empty($vars['elements']['#data']['css'])) {

    if (!empty($vars['elements']['#grid_container'])) {
      $vars['content_attributes_array']['class'][] = 'container'; # Добавляем класс container зоне
    }

    $vars['content_attributes'] = $vars['content_attributes_array'] ? drupal_attributes($vars['content_attributes_array']) : '';
  }

  alpha_invoke('process', $hook, $vars);
}

/**
 * Implements theme_delta_blocks_breadcrumb().
 */
function omega_bootstrap_delta_blocks_breadcrumb($variables) {
  $output = '';
   
  if (!empty($variables['breadcrumb'])) {  
    if ($variables['breadcrumb_current']) {
      $variables['breadcrumb'][] = l(drupal_get_title(), current_path(), array('html' => TRUE));
    }
  
    $output = '<div id="breadcrumb" class="clearfix"><ul class="breadcrumb">';
    $switch = array('odd' => 'even', 'even' => 'odd');
    $zebra = 'even';
    $last = count($variables['breadcrumb']) - 1;    
    
    foreach ($variables['breadcrumb'] as $key => $item) {
      $zebra = $switch[$zebra];
      $attributes['class'] = array('depth-' . ($key + 1), $zebra);
      
      if ($key == 0) {
        $attributes['class'][] = 'first';
      }
      
      if ($key == $last) {
        $attributes['class'][] = 'last';
      }
      else {
        $item .= '<span class="divider">/</span>';
      }

      $output .= '<li' . drupal_attributes($attributes) . '>' . $item . '</li>';
    }
      
    $output .= '</ul></div>';
  }
  
  return $output;
}

/**
 * Implements theme_status_messages().
 */
function omega_bootstrap_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'), 
    'error' => t('Error message'), 
    'warning' => t('Warning message'),
  );
  
  $class = array(
    'status' => 'alert alert-success', 
    'error' => 'alert alert-error', 
    'warning' => 'alert',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"{$class[$type]}\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Implements theme_menu_local_tasks().
 */
function omega_bootstrap_menu_local_tasks(&$variables) {
  $output = '';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<ul class="nav nav-pills">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<ul class="nav nav-pills">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }

  return $output;
}

/**
 * Implements theme_button().
 */
function omega_bootstrap_button($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'btn';
  
  switch($element['#id']) { # Разукрашиваем основные кнопки
    case strpos($element['#id'], 'edit-submit') === 0: $element['#attributes']['class'][] = 'btn-primary'; break;
    case 'edit-preview': $element['#attributes']['class'][] = 'btn-warning'; break;
    case 'edit-delete': $element['#attributes']['class'][] = 'btn-danger'; break;
  }

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled btn-disabled';
  }

  return ' <input' . drupal_attributes($element['#attributes']) . ' /> ';
}

/**
 * Implements theme_pager().
 */
function omega_bootstrap_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('« first')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('last »')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'data' => '<span>…</span>',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('active'), 
            'data' => "<span>$i</span>",
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'data' => '<span>…</span>',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'data' => $li_last,
      );
    }
    return '<h2 class="element-invisible">' . t('Pages') . '</h2><div class="pagination pagination-centered">' . theme('item_list', array(
      'items' => $items, 
    )) . '</div>';
  }
}