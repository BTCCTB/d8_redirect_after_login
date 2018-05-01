<?php
 
/**
 
 * @file
 
 * Contains \Drupal\login_redirection\Form\LoginRedirectionForm.
 
 */
 
namespace Drupal\login_redirection\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class LoginRedirectionForm extends ConfigFormBase {
  public  $allUser=[];
      /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_redirection_form';
  }
    /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  $savedPathRoles=\Drupal::config('login_redirection.settings')->get('login_redirection');
  $this->allUser = user_role_names();
  $form['roles'] = array(
    '#type' => 'fieldset',
    '#title' => t('All roles'),
  );
  foreach ($this->allUser as $user=>$name){
    $form['roles'][$user] = [
      '#type' => 'textfield',
      '#title' => t($name),
	  '#size' => 60,
	  '#maxlength' => 128,
          '#description' => t('Add a valid url or <\front> for main page'),
	  '#required' => TRUE,
          '#default_value' => $savedPathRoles[$user],
    ];
  }
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

    /**
   * {@inheritdoc}
   */
public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($this->allUser as $user=>$name){
      if(!preg_match('/[#?\/]+/',$form_state->getValue($user))){
         $form_state->setErrorByName($form_state->getValue($user), t('This URL is not valid.'));
      }
    }
}
      /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $loginUrls=[];
    foreach ($this->allUser as $user=>$name){
      if($form_state->getValue($user)=='<front>'){
        $loginUrls[$user]='/';
      }
      else{ 
        $loginUrls[$user]=$form_state->getValue($user);
        $form_state->getValue($user);
      }
    }
    $this->config('login_redirection.settings')->set('login_redirection', $loginUrls)->save();
    
    parent::submitForm($form, $form_state);
}
    /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
    'login_redirection.settings'
    ];
  }

}