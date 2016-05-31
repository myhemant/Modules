<?php

/**
 * @file
 * Contains \Drupal\devel\Form\SettingsForm.
 */

namespace Drupal\pgwslider\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures global PgwSlider settings.
 */
class SettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormID() {
        return 'pgwslider_admin_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            'pgwslider.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Get all settings
        $settings = $this->config('pgwslider.settings')->get();
        $form['pgwslider']['pgwslider_admin_intro'] = array(
            '#markup' => t("The below options will apply to all PGWslider."),
        );

        // enable disable the counter.
        $form['pgwdisplay_count'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show the slider counter'),
            '#default_value' => $settings['pgwdisplay_count'],
        );

        // How to transition between slides.
        $form['pgwtransition_effect'] = array(
            '#type' => 'select',
            '#title' => t('Transition effect'),
            '#default_value' => $settings['pgwtransition_effect'],
            '#options' => array(
                'sliding' => t('Sliding'),
                'fading' => t('Fading'),
            ),
        );
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('pgwslider.settings')
                ->set('pgwdisplay_count', (bool) $form_state->getValue('pgwdisplay_count'))
                ->set('pgwtransition_effect', $form_state->getValue('pgwtransition_effect'))
                ->save();

        parent::submitForm($form, $form_state);
    }

}
