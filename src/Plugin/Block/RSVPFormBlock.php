<?php

namespace Drupal\rsvp_event\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

/**
 * Provides a rsvpform block.
 *
 * @Block(
 *   id = "rsvp_event_rsvpform",
 *   admin_label = @Translation("RSVPForm"),
 *   category = @Translation("RSVP"),
 * )
 */
class RSVPFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // @todo, Need to check if user already signup for this event.

    if ($account->isAnonymous()) {
      return AccessResult::allowedIf(FALSE);
    }
    // Threshold distance in mile.
    $threshold_distance = 20;
    $distance = $this->getDistance($account);
    $condition = FALSE;
    if ($distance <= $threshold_distance) {
      $condition = TRUE;
    }

    return AccessResult::allowedIf($condition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('\Drupal\rsvp_event\Form\RSVPForm');
    return $form;
  }

  protected function getDistance(AccountInterface $account) {
    $node = $this->getContextValue('node');
    $user = User::load($account->id());
    $user_loaction = !$user->get('field_location')->isEmpty() ? $user->get('field_location')->first()->getValue() : NULL;
    $node_location = !$node->get('field_location')->isEmpty() ? $node->get('field_location')->first()->getValue() : NULL;

    if (!empty($user_loaction) &&  !empty($node_location)) {
      $user_lat = $user_loaction['lat'];
      $user_lng = $user_loaction['lng'];
      $node_lat = $node_location['lat'];
      $node_lng = $node_location['lng'];

      $theta = $user_lng - $node_lng;
      $dist = sin(deg2rad($user_lat)) * sin(deg2rad($node_lat)) +  cos(deg2rad($user_lat)) * cos(deg2rad($node_lat)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      return $miles;
    }
    return 0;
  }

}
