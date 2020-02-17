<?php

namespace Drupal\rsvp_event;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Lookup service.
 */
class RSVPEventLookup {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  private $routeMatch;

  /**
   * Constructs a Lookup object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager,
                              AccountProxyInterface $current_user,
                              RouteMatchInterface $route_match) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->routeMatch = $route_match;
  }

  /**
   * Threshold distance in mile.
   */
  public function getThresholdValue() {
    return 20;
  }

  /**
   * Check if a relation exists between current user and event.
   *
   * @param int $nid
   *   The event id.
   *
   * @return bool|null
   *   If there's no record, return FALSE; otherwise, return TRUE.
   */
  public function getEventConfirmationForCurrentUser($nid) {
    if (empty($nid)) {
      return NULL;
    }

    // Get the current user id.
    $uid = $this->currentUser->id();

    $query = $this->entityTypeManager->getStorage('rsvp_confirmation')->getQuery();
    $query->condition('uid', $uid);
    $query->condition('nid', $nid);
    $matched_entity = $query->execute();

    return !empty($matched_entity);
  }

  /**
   * Get the list of RSVP'd users.
   *
   * @param int $nid
   *   The event id.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|null
   *   Returns list of users for the event.
   */
  public function getUserListforEvent($nid) {
    if (empty($nid)) {
      return NULL;
    }
    $query = $this->entityTypeManager->getStorage('rsvp_confirmation');
    $rsvp_confirmations = $query->loadByProperties(['nid' => $nid]);

    return $rsvp_confirmations;
  }

  /**
   * Get the list of RSVP'd user names.
   *
   * @param int $nid
   *   The event id.
   *
   * @return array|null
   *   Returns list of user name for the event.
   */
  public function getUserNameList($nid) {
    if (empty($nid)) {
      return NULL;
    }
    $rsvp_confirmations = $this->getUserListforEvent($nid);
    $user_list = [];
    if (!empty($rsvp_confirmations)) {
      foreach ($rsvp_confirmations as $rsvp_confirmation) {
        if (!$rsvp_confirmation->isHideMe()) {
          $user_list[] = $rsvp_confirmation->getUserName();
        }
      }
    }

    return $user_list;
  }

  /**
   * Get the distance between the user and event locations.
   *
   * @param int $nid
   *   The event id.
   *
   * @return float|int
   *   Returns the distance
   */
  public function getRSVPDistance($nid) {
    $miles = 0;
    if (empty($nid)) {
      return $miles;
    }

    // Get the node.
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    // Get the user.
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());

    $user_loaction = !$user->get('field_location')->isEmpty() ? $user->get('field_location')->first()->getValue() : NULL;
    $node_location = !$node->get('field_location')->isEmpty() ? $node->get('field_location')->first()->getValue() : NULL;

    if (!empty($user_loaction) &&  !empty($node_location)) {
      $user_lat = $user_loaction['lat'];
      $user_lng = $user_loaction['lng'];
      $node_lat = $node_location['lat'];
      $node_lng = $node_location['lng'];

      $theta = $user_lng - $node_lng;
      $dist = sin(deg2rad($user_lat)) * sin(deg2rad($node_lat)) + cos(deg2rad($user_lat)) * cos(deg2rad($node_lat)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
    }
    return $miles;
  }

}
