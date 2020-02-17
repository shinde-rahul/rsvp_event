<?php

namespace Drupal\rsvp_event\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\ContextDefinition;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvp_event\RSVPEventLookup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a rsvp user list block.
 *
 * @Block(
 *   id = "rsvp_event_rsvp_user_list",
 *   admin_label = @Translation("RSVP User List"),
 *   category = @Translation("RSVP"),
 *   context_definitions = {
 *      "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
class RSVPUserListBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The rsvp_event.lookup service.
   *
   * @var Drupal\rsvp_event\RSVPEventLookup
   */
  protected $rsvpEventLookup;

  /**
   * Constructs a new RSVPUserListBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param Drupal\rsvp_event\RSVPEventLookup $rsvp_event_lookup
   *   The rsvp_event.lookup service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RSVPEventLookup $rsvp_event_lookup) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->rsvpEventLookup = $rsvp_event_lookup;
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
    // @DCG Evaluate the access condition here.
    $condition = TRUE;
    return AccessResult::allowedIf($condition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getContextValue('node');
    $rsvp_used = $this->rsvpEventLookup->getUserNameList($node->id());
    $build['content'] = [
      '#markup' => $this->t('It works 12!'),
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $rsvp_used,
      '#empty' => t('No sign ups yet!!'),
      '#attributes' => ['class' => 'users'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
