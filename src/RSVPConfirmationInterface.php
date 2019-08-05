<?php

namespace Drupal\rsvp_event;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a rsvp confirmation entity type.
 */
interface RSVPConfirmationInterface extends ConfigEntityInterface {

  /**
   * Get the user id.
   *
   * @return int|null
   *   The user ID, or NULL if the object does not yet have a
   *   user ID.
   */
  public function getUid();

  /**
   * Get the nid.
   *
   * @return int|null
   *   The node id, or NULL if the object does not yet have a
   *   node ID.
   */
  public function getNid();

  /**
   * Get the username.
   *
   * @return string|null
   *   The username, or NULL if the object does not yet have a username.
   */
  public function getUserName();

  /**
   * Get the user's email.
   *
   * @return string|null
   *   The user's email, or NULL if the object does not yet have a user email.
   */
  public function getUserEmail();

  /**
   * Get the event name.
   *
   * @return string|null
   *   The event name, or NULL if the object does not yet have a event name.
   */
  public function getEventName();

  /**
   * Get the hide me value.
   *
   * @return bool
   *   The users preferences to hide from the list.
   */
  public function isHideMe();

}
