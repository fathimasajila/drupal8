<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\AddCurrency.
 */

namespace Drupal\custom_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;



/**
 * Handles user listing.
 */
class UserListController extends ControllerBase  {

  
  /**
   * Get User list
   *
   * @return array
   *   A renderable array.
   */
  public function get_user_list() {
    $user_storage = \Drupal::service('entity_type.manager')->getStorage('user');

$ids = $user_storage->getQuery()
  ->condition('status', 1)
  ->execute();
$users = $user_storage->loadMultiple($ids);
foreach ($users as $user) {
  $name = $user->name->value;

}
    $build = [
      '#markup' => $name,
    ];
    return $build;
  }
}
