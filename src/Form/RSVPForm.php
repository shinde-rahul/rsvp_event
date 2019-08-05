<?php

namespace Drupal\rsvp_event\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvp_event\Entity\RSVPConfirmation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RSVPForm.
 *
 * @package Drupal\rsvp_event\Form
 */
class RSVPForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * RSVPForm constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The Current user.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(AccountInterface $account, RouteMatchInterface $route_match) {
    $this->account = $account;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvp_event_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Checkbox to hide from the list of attendees.
    $form['hide_me'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide me from the list of attendees.'),
    ];

    // Attach Ajax callback to the Submit button.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('RSVP'),
      '#ajax' => [
        'callback' => '::setRSVP',
        'wrapper' => 'rsvp-form-wrapper',
        'method' => 'replace',
        'effect' => 'fade',
      ],
    ];

    $form['#prefix'] = '<div id="rsvp-form-wrapper">';
    $form['#suffix'] = '</div>';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do. We have used Ajax.
  }

  /**
   * Callback function to set the RSVPConfirmation.
   */
  public function setRSVP(array $form, FormStateInterface $form_state) {
    $uid = $this->account->id();
    $node = $this->routeMatch->getParameter('node');

    // Create RSVP confirmation relation.
    $confirmation = RSVPConfirmation::create([
      'id' => time(),
      'uid' => intval($uid),
      'nid' => intval($node->id()),
      'hide_me' => $form_state->getValue('hide_me'),
    ]);

    // Save RSVP confirmation.
    $confirmation->save();

    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '#rsvp-form-wrapper',
        '<div class="rsvp-form">' . $this->t("You have successfully RSVP'd for %event", ['%event' => $confirmation->getEventName()]) . '</div>'
      )
    );
    return $response;
  }

}
