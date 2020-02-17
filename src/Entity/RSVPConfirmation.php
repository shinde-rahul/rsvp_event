<?php

namespace Drupal\rsvp_event\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\node\Entity\Node;
use Drupal\rsvp_event\RSVPConfirmationInterface;
use Drupal\user\Entity\User;

/**
 * Defines the rsvp confirmation entity type.
 *
 * @ConfigEntityType(
 *   id = "rsvp_confirmation",
 *   label = @Translation("RSVP Confirmation"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rsvp_event\RsvpConfirmationListBuilder",
 *     "form" = {
 *       "add" = "Drupal\rsvp_event\Form\RSVPConfirmationForm",
 *       "edit" = "Drupal\rsvp_event\Form\RSVPConfirmationForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *   },
 *   config_prefix = "rsvp_confirmation",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "uid",
 *     "nid" = "nid",
 *     "hide_me" = "hide_me",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "collection" = "/admin/structure/rsvp-confirmation",
 *     "add-form" = "/admin/structure/rsvp-confirmation/add",
 *     "edit-form" = "/admin/structure/rsvp-confirmation/{rsvp_confirmation}",
 *     "delete-form" = "/admin/structure/rsvp-confirmation/{rsvp_confirmation}/delete"
 *   },
 * )
 */
class RSVPConfirmation extends ConfigEntityBase implements RSVPConfirmationInterface {

  /**
   * The rsvp confirmation ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The user id.
   *
   * @var string
   */
  protected $uid;

  /**
   * The node id.
   *
   * @var string
   */
  protected $nid;

  /**
   * The hide me.
   *
   * @var bool
   */
  protected $hide_me;

  /**
   * Get user ID.
   *
   * @return int
   *   User ID.
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * Get node ID.
   *
   * @return int
   *   Node ID.
   */
  public function getNid() {
    return $this->nid;
  }

  /**
   * Get the username.
   *
   * @return string|null
   *   User's username.
   */
  public function getUserName() {
    $account = User::load($this->uid);
    $name = $account->getUsername();

    return $name;
  }

  /**
   * Get the user email.
   *
   * @return string|null
   *   The user's email.
   */
  public function getUserEmail() {
    $account = User::load($this->uid);
    $email = $account->getEmail();

    return $email;
  }

  /**
   * Get the event name.
   *
   * @return string|null
   *   The event name.
   */
  public function getEventName() {
    $node = Node::load($this->nid);
    $name = $node->getTitle();

    return $name;
  }

  /**
   * Get the hide me .
   *
   * @return bool|null
   *   The hide me value.
   */
  public function isHideMe() {
    return $this->hide_me;
  }

}
