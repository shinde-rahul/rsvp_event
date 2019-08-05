<?php

namespace Drupal\rsvp_event\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * RSVP Confirmation form.
 *
 * @property \Drupal\rsvp_event\RSVPConfirmationInterface $entity
 */
class RSVPConfirmationForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\rsvp_event\Entity\RSVPConfirmation::load',
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    // User ID.
    $form['uid'] = [
      '#type' => 'number',
      '#title' => $this->t('User ID'),
      '#description' => $this->t("The ID of the user who RSVP'd for this event."),
      '#default_value' => $this->entity->getUid(),
      '#machine_name' => [
        'exists' => '\Drupal\rsvp_event\Entity\RSVPConfirmation::load',
      ],
      '#disabled' => !$this->entity->isNew(),
      '#required' => TRUE,
    ];

    // Node ID.
    $form['nid'] = [
      '#type' => 'number',
      '#title' => $this->t('Node ID'),
      '#description' => $this->t('The ID of the event node.'),
      '#default_value' => $this->entity->getNid(),
      '#machine_name' => [
        'exists' => '\Drupal\rsvp_event\Entity\RSVPConfirmation::load',
      ],
      '#disabled' => !$this->entity->isNew(),
      '#required' => TRUE,
    ];

    // Hide Me.
    $form['hide_me'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide Me'),
      '#description' => $this->t('Flag to hide from the list.'),
      '#default_value' => $this->entity->isHideMe(),
      '#machine_name' => [
        'exists' => '\Drupal\rsvp_event\Entity\RSVPConfirmation::load',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);
    $message_args = [
      '%user' => $this->entity->getUserName(),
      '%event' => $this->entity->getEventName(),
    ];
    $message = $result == SAVED_NEW
      ? $this->t("You have successfully added %user's RSVP for %event.", $message_args)
      : $this->t("You have successfully updated %user's RSVP for %event.", $message_args);
    $this->messenger()->addStatus($message);
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $result;
  }

}
