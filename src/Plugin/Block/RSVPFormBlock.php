<?php

namespace Drupal\rsvp_event\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvp_event\RSVPEventLookup;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a rsvpform block.
 *
 * @Block(
 *   id = "rsvp_event_rsvpform",
 *   admin_label = @Translation("RSVPForm"),
 *   category = @Translation("RSVP"),
 *   context_definitions = {
 *      "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
class RSVPFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The RSVP event lookup.
   *
   * @var Drupal\rsvp_event\RSVPEventLookup|RSVPEventLookup
   */
  private $eventLookup;

  /**
   * RSVPFormBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param Drupal\rsvp_event\RSVPEventLookup $event_lookup
   *   The RSVP Event lookup.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RSVPEventLookup $event_lookup) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->eventLookup = $event_lookup;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('rsvp_event.lookup')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // @todo, Need to check if user already signup for this event.

    if ($account->isAnonymous()) {
      return AccessResult::allowedIf(FALSE);
    }
    // Threshold distance in mile.
    $threshold_distance = $this->eventLookup->getThresholdValue();
    $node = $this->getContextValue('node');
    $distance = $this->eventLookup->getRSVPDistance($node->id());
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

}
