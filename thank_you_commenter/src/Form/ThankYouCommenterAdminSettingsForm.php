<?php

/**
 * @file
 * Contains \Drupal\thank_you_commenter\Form\ThankYouCommenterAdminSettingsForm.
 */

namespace Drupal\thank_you_commenter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure thank_you_commenter settings for this site.
 */
class ThankYouCommenterAdminSettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormID() {
        return 'thank_you_commenter_admin_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            'thank_you_commenter.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        /* Get the content types names. */
        $options = array();
        $content_types = node_type_get_names();
        foreach ($content_types as $name => $content_type) {
            $options[$name] = $content_type;
        }

        $config = \Drupal::config('thank_you_commenter.settings');
        $tyc_content_types = $config->get('tyc_content_types') ?
                $config->get('tyc_content_types') : array();

        $form['tyc_admin_intro'] = array(
            '#markup' => t("Thank You Commenter Admin Settings"),
        );
        // Mail send for registered user
        $form['tyc_registered_users'] = array(
            '#type' => 'checkbox',
            '#title' => t('Authenticated Users'),
            '#description' => t('Mail will be send to registered users only.'),
            '#default_value' => $config->get('tyc_registered_users'),
        );

        // Mail send for anonymous user
        $form['tyc_anonymous_users'] = array(
            '#type' => 'checkbox',
            '#title' => t('Anonymous Users'),
            '#description' => t('Mail will be send to anonymous users only if they
      provide their email.'),
            '#default_value' => $config->get('tyc_anonymous_users'),
        );

        // Applicable on content types
        $form['tyc_content_types'] = array(
            '#type' => 'checkboxes',
            '#title' => t('Content Types'),
            '#description' => t('The above will be applicable on these content types only.'),
            '#options' => $options,
            '#default_value' => $tyc_content_types,
        );

        // Mail message for authenticated user
        $form['tyc_authenticated_user_mailtext'] = array(
            '#type' => 'textarea',
            '#title' => t('Authenticated Users Mailtext'),
            '#description' => t('The mail text that will be send to authenticated users.'),
            '#default_value' => $config->get('tyc_authenticated_user_mailtext'),
        );

        // Mail message for anonymous user
        $form['tyc_anonymous_user_mailtext'] = array(
            '#type' => 'textarea',
            '#title' => t('Anonymous Users Mailtext'),
            '#description' => t('The mail text that will be send to anonymous users.'),
            '#default_value' => $config->get('tyc_anonymous_user_mailtext'),
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('thank_you_commenter.settings')
                ->set('tyc_registered_users', $form_state->getValue('tyc_registered_users'))
                ->set('tyc_anonymous_users', $form_state->getValue('tyc_anonymous_users'))
                ->set('tyc_content_types', $form_state->getValue('tyc_content_types'))
                ->set('tyc_authenticated_user_mailtext', $form_state->getValue('tyc_authenticated_user_mailtext'))
                ->set('tyc_anonymous_user_mailtext', $form_state->getValue('tyc_anonymous_user_mailtext'))
                ->save();

        parent::submitForm($form, $form_state);
    }

}
